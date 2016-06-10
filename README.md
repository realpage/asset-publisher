## Asset Publisher [![Build Status](https://travis-ci.org/realpage/asset-publisher.svg?branch=master)](https://travis-ci.org/realpage/asset-publisher)

This service responds to GitHub web hooks looking for semantically versioned tags and deploying minor and patch versions (i.e. - v1.2/v1.2.3) to a distributor (i.e. - Amazon S3).

## Usage

**Environment Variables**

 * `PRIVATE_KEY` - Used by the _CLI container_ to gain access to private repositories
 * `NAMESPACE` - Deploy assets in a subdirectory (e.g. `https://s3.amazonaws.com/my-bucket/[NAMESPACE]/v1.0.0/...`)
 * `BUILD_PATH` - Relative path to the directory within the git repository that should be deployed to s3.  Defaults to `build`

## Amazon S3

 * `AWS_KEY`
 * `AWS_SECRET`
 * `AWS_BUCKET`
 * `AWS_REGION` (defaults to `us-east-1`
 
  > Despite the use of flysystem, distributors other than Amazon S3 are not supported at this time due to a limitation in uploading directories via flysystem.