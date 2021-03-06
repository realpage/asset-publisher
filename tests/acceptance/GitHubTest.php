<?php

namespace Tests\App;

use GrahamCampbell\Flysystem\Facades\Flysystem;
use GrahamCampbell\Flysystem\FlysystemManager;
use TestCase;
use Log;

class GitHubTest extends \TestCase
{
    protected $patchVersion = 'v1.1.0';
    protected $minorVersion = 'v1.1';

    /** @var FlysystemManager */
    protected $flysystem;

    public function setUp()
    {
        parent::setUp();

        $this->flysystem = app('flysystem');

        # In case there was a previous failure, make sure we're starting clean
        if ($this->flysystem->has('/'.$this->patchVersion)) {
            $this->flysystem->deleteDir('/'.$this->patchVersion);
        }
        if ($this->flysystem->has('/'.$this->minorVersion)) {
            $this->flysystem->deleteDir('/' . $this->minorVersion);
        }
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->flysystem->deleteDir('/'.$this->patchVersion);
        $this->flysystem->deleteDir('/'.$this->minorVersion);
    }

    /**
     * Note: This test is NOT run in CI as there is a permissions issue with the
     * SSH key that stops the build from continuing.  This is likely a docker
     * issue so when the docker version is updated we should look into enabling
     * this test again at a later time.
     *
     * @test
     */
    public function respondsToGitHubHookAndDeploysToS3()
    {
        // avoid extra logging when running tests
        Log::shouldReceive('info');

        // for full request body, see https://developer.github.com/v3/activity/events/types/#createevent
        $this->json('POST', '/webhook/'.env('TOKEN'), [
            'action' => 'published',
            'release' => [
                'tag_name' => 'v1.1.0'
            ],
            "repository" => [
                'full_name' => 'realpage/asset-publisher-test'
            ]
        ], [
            'X-GitHub-Event' => 'release'
        ])
            ->assertResponseOk();

        $this->assertTrue($this->flysystem->has('/'.$this->minorVersion.'/deploy-me.json'));
        $this->assertTrue($this->flysystem->has('/'.$this->patchVersion.'/deploy-me.json'));
    }
}