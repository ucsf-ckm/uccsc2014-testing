Feature: Slide Navigation
  In order for to ensure an easy flow of the presentation
  As a presenter
  The slideshow must be navigable via keyboard shortcuts

  Background:
    Given I am on the home page

  Scenario: Advance to next slide
    Then I should only see the 1st slide
    When I press the "right" key
    Then I should only see the 2nd slide
    When I press the "right" key
    Then I should only see the 3rd slide

  Scenario: Revert to previous slide
    And I press the "right" key
    And I press the "right" key
    Then I should only see the 3rd slide
    When I press the "left" key
    Then I should only see the 2nd slide
    When I press the "left" key
    Then I should only see the 1st slide
