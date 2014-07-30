<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Context\Step\When,
    Behat\Behat\Context\Step\Then,
    Behat\Behat\Context\Step\Given,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use Behat\MinkExtension\Context\MinkContext;

/**
 * Features context.
 */
class FeatureContext extends MinkContext
{
    /**
     * @var array configuration parameters
     *
     */
    protected $params;

    /**
     * Override the constructor to get access to the configuration params
     *
     */
    public function __construct(array $arr)
    {
        $this->params = $arr;
    }

    /**
     * Helper function for slow loading pages. http://docs.behat.org/cookbook/using_spin_functions.html
     * Needed for Sauce Labs integration to work consistently probably because...
     * ...initial page load includes a lot of resources and may need some extra time to complete.
     * @todo: remove unnecessary elements, move elements that don't need to be in the critical path out of the critical path, and get rid of this
     */
    public function spin ($lambda, $wait = 60)
    {
        for ($i = 0; $i < $wait; $i++)
        {
            try {
                if ($lambda($this)) {
                    return true;
                }
            } catch (Exception $e) {
                // do nothing
            }

            sleep(1);
        }

        $backtrace = debug_backtrace();

        throw new Exception(
            "Timeout thrown by " . $backtrace[1]['class'] . "::" . $backtrace[1]['function'] . "()"
        );
    }

    /**
     * @Given /^I am on the home page$/
     */
    public function iAmOnTheHomePage ()
    {
        $this->visit("/");
        $context = $this;
        $this->spin(function($context) {
            return (count($context->getSession()
                ->getPage()->findById('source')) > 0);
        });
    }

    /**
     * When a step fails take a screen shot.
     * This should work with any of the selenium2 drivers incuding phantomjs
     *
     * It outputs the file location above the failed step.
     * @todo find a better way to techo the path to the console.
     * @AfterStep
     */
    public function takeScreenshotAfterFailedStep($event)
    {
        if ($event->getResult() == 4) {
            if ($this->getSession()->getDriver() instanceof \Behat\Mink\Driver\Selenium2Driver) {
                $stepText = $event->getStep()->getText();
                $fileTitle = preg_replace("#[^a-zA-Z0-9\._-]#", '', $stepText);
                $fileName = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $fileTitle . '.png';
                $screenshot = $this->getSession()->getDriver()->getScreenshot();
                file_put_contents($fileName, $screenshot);
                print "Screenshot for '{$stepText}' placed in {$fileName}\n";
            }
        }
    }

    /**
     * @Then /^I should see the (\d+)(?:st|nd|rd|th) slide/
     */
    public function iShouldSeeTheNthSlide($n)
    {
        $activeSlideId = 'slide-' . ($n - 1); // slides are zero-indexed.
        $slides = $this->getSession()->getPage()->findAll('css', '.slide');
        if (! $slides) {
            throw new Exception("Couldn't find any slides on the page.");
        }
        $activeSlideFound = false;
        foreach ($slides as $slide) {
            $currentSlideId = $slide->getAttribute('id');

            if ($currentSlideId === $activeSlideId) {
                $activeSlideFound = true;
                if (! $slide->isVisible()) {
                    throw new Exception(sprintf("The %d. slide should be visible, but it is not.", $n));
                }
            }
        }

        if (! $activeSlideFound) {
            throw new Exception (sprintf("The %d. slide does not exist on page.", $n));
        }
    }

    /**
     * @When /^I press the right key$/
     */
    public function iPressTheRightKey()
    {
        $this->pressKeyOnPage(39);
        sleep(1); // wait out transition
    }

    /**
     * @When /^I press the left key$/
     */
    public function iPressTheLeftKey()
    {
        $this->pressKeyOnPage(37);
        sleep(1); // wait out transition

    }

    /**
     * Press a key on the page using jQuery.
     * @param int $keyCode The code of the key to press.
     * @link http://stackoverflow.com/a/17521635/307333
     */
    private function pressKeyOnPage($keyCode)
    {
        $script = "jQuery(document).trigger(jQuery.Event('keydown', { which: ${keyCode}, keyCode: ${keyCode}}));";
        $this->getSession()->evaluateScript($script);
    }
}
