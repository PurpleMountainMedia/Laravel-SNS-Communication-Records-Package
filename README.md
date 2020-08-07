# Laravel SNS Communication Records
A package to track and store events from AWS SNS, particularly those generated from SES events such as sent, delivered, clicked, bounced and complaints.

## Installation

```
composer require purplemountain/sns-communication-records
```

## Setup
1. Setup SES and SNS in the AWS console. (Documentation to follow!)

## Env
You will need to add the following details to your `.env` file and paste in the values from Zoho.

```
MAIL_MAILER=ses
MAIL_FROM_ADDRESS=noreply@mail.purplemountmedia.com

AWS_ACCESS_KEY_ID=<AWS_KEY>
AWS_SECRET_ACCESS_KEY=<AWS_SECRET>
```
