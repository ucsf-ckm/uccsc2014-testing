# UCCSC 2014 Testing Session

Session Proposal: [Test your application or website in every browser for free while you have coffee.]](https://uccsc.ucsf.edu/session/test-your-application-or-website-every-browser-free-while-you-have-coffee)

This uses [deck.js](http://imakewebthings.github.com/deck.js/docs). All the action is in `index.html`.

##This presentation demonstrates itself

In order to demonstrate the integrations we wrote some very easy tests against this presentation.  If you clone this repository you can run these tests for yourself.

###Installation
1. Install Composer. See http://getcomposer.org/doc/00-intro.md for instructions.

2. Install Behat and co. via Composer.
    composer install

###Run tests

You now need to setup your sauceusername and accesskey.  These can be found at https://saucelabs.com/account.
    export SAUCE_USERNAME=<username>
    export SAUCE_ACCESS_KEY=<accesskey>

And then do the same thing to configure sauce connect:
    bin/sauce_config <username> <accesskey>

It is now possible to use sauce to test in many different browsers.  A listing of available setups can be found in `tests/behat.yml`.
here are some examples:

1. Windows 7 IE9

    bin/behat -c tests/behat.yml -p win7ie9

2. Windows 8 IE11

    bin/behat -c tests/behat.yml -p win8ie11

3. Mac OSX Safari

    bin/behat -c tests/behat.yml -p osxsafari

4. Mac OSX Firefox

    bin/behat -c tests/behat.yml -p osxfirefox
