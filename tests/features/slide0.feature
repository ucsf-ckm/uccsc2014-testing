Feature: Slide 0
  In order to be effective the slideshow has to start with Slide 0 and show
  summary and author information

  Background:
    Given I am on the home page

  Scenario: View Authors
    Then I should see "Stefan Topfstedt"
    And I should see "Jon Johnson"
