[![Build status — Travis-CI][travis-icon]][travis]
[![School closures - MK][badge]][stat]

# school-closure

A PHP script to extract / scrape data on school snow closures in a town.

Example data: [index.json][]

## Install .. test

```sh
composer install && composer npm-install
composer copy-env
composer test
```

## Robot

### Bot

I identify myself with the `User-Agent` (and I sleep between requests):

```
SchoolClosure/1.0-beta +https://github.com/nfreear/school-closure#bot
```

### Cron

Example [crontab][] (assumes the server is configured for your local time):

```
# min hr  dom mon day  command
*/10  6-9 *   *   *    cd /path/to/school-closure; /usr/local/bin/composer cron
```

### Legal

All data remains the property of the web-site publisher,
and the web-site remains the definitive/ master copy of the data.
Use at your own risk.
I accept no liability for losses.

License: [MIT][].


[mit]: https://nfreear.mit-license.org/2017
[gh]: https://github.com/nfreear/school-closure
[travis]: https://travis-ci.org/nfreear/school-closure "Build status — Travis-CI"
[travis-icon]: https://travis-ci.org/nfreear/school-closure.svg
[index.json]: http://iet-embed-acct.open.ac.uk/dev/school-closure/index.json
[badge]: http://iet-embed-acct.open.ac.uk/dev/school-closure/badge-svg/?s=Middleton%20Primary
[stat]: https://www.milton-keynes.gov.uk/closures?page=8#results
  "School closures - Milton Keynes"

[cront-0]: https://crontab.guru/#*/10_6-9_*_*_*__cd_path;_composer_cron
[crontab]: https://crontab.guru/#*/10_6-9_*_*_*
  "crontab.guru ~ “At every 10th minute past every hour from 6 through 9.”"

[End]: //.
