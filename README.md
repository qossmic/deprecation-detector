# SensioLabs DeprecationDetector

[![Build Status](https://img.shields.io/travis/sensiolabs-de/deprecation-detector/master.svg?style=flat-square)](https://travis-ci.org/sensiolabs-de/deprecation-detector)
[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/sensiolabs-de/deprecation-detector?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge)

The SensioLabs DeprecationDetector runs a static code analysis against your project's source code to find usages of deprecated methods, classes and interfaces. For Symfony2 projects, it also detects usages of deprecated services.
It identifies the use of deprecated code thanks to the `@deprecated` annotation.

### Disclaimer

**Please note this tool is in a very early stage of development. Expect bugs and quirks.** 

## Basic knowledge

The Sensiolabs DeprecationDetector is a command line command based on the [Symfony2 console component](https://github.com/symfony/Console).
It makes heavy use of the [PHP-Parser](https://github.com/nikic/PHP-Parser) library for analyzing PHP code.  

The command works in three steps:

1. Scanning your vendor libraries for defined deprecations and cache them as a `ruleset`
2. Finding usages of those deprecations from your ruleset.
3. Output with the affected code parts.

## Installation

### Standalone installation

Clone the repository

```bash
$ git clone git@github.com:sensiolabs-de/deprecation-detector.git
```

Run composer

```bash
$ composer install
```

Create phar archive with [Box](http://box-project.github.io/box2/) (optional)

```bash
$ box build
```

If you want to call the deprecation-detector globally, it needs to be placed in your `PATH`. For example with:

```bash
$ sudo chmod a+x deprecation-detector
$ sudo mv deprecation-detector /usr/local/bin/deprecation-detector
```

## Usage


To use the DeprecationDetector you need to provide the `source` and the `ruleset` arguments

```bash
$ deprecation-detector check src/ vendor/
$ deprecation-detector check src/ composer.lock
$ deprecation-detector check src/ .rules/some_generated_rule_set
```

### Output

There are different output formats available, by default the output is printed in the commandline.

#### Html
```bash
$ deprecation-detector check src/ vendor/ --log-html deprecationlog.html
```

You can get a list of all options and arguments by running

```bash
$ deprecation-detector check --help
```

## Excluding deprecated method calls

You can exclude deprecated method calls by using the `filter-methods` option. This option takes a comma separated list of method references to exclude.

```bash
$ deprecation-detector check --filter-methods=MyClass::method,Foo::bar src/ vendor/
```

This will exclude all deprecated calls to MyClass::method and Foo::bar.


## Contribution

Currently, the SensioLabs DeprecationDetector is in a very early state. Pull requests are welcome!


## Maintainers

The DeprecationDetector is a project developed by the team at [SensioLabs Deutschland](http://sensiolabs.de/), maintained by [@marvin_klemp](https://twitter.com/marvin_klemp).
