# Magento 2 Sentry Logger

[![Build Status](https://github.com/mygento/module-sentry/actions/workflows/grumphp.yml/badge.svg)](https://github.com/mygento/module-sentry/actions/workflows/grumphp.yml)
[![Latest Stable Version](https://poser.pugx.org/mygento/module-sentry/v/stable)](https://packagist.org/packages/mygento/module-sentry)
[![License](https://poser.pugx.org/mygento/module-sentry/license)](https://packagist.org/packages/mygento/module-sentry)
[![Total Downloads](https://poser.pugx.org/mygento/module-sentry/downloads)](https://packagist.org/packages/mygento/module-sentry)

This extension add the ability to send errors to [Sentry](https://sentry.io/) with configurable log-level.
No overrides or preferences in di.xml

## Installation

Install the extension using [Composer](https://getcomposer.org/).

```bash
composer require mygento/module-sentry
```

### Configuration

- Enable in store configuration (System -> Configuration -> Mygento extensions -> Sentry)
- Set your DSN
- Set environment
- Optionally set other options

### Compability

The module is tested on magento version 2.4.x with Sentry SDK version 4.x