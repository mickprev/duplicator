Feature: Duplicate
    As a product contributor,
    I should be able to duplicate a product
    In order to contribute more easily

    Scenario: I can duplicate all useful information of a product
        Given I have all groups in the context
        When I duplicate the easybreath product
        Then a new product should have been created
        And the new product should have 1 category
        And the new product should have 1 advice
        And the new product should have 2 technical informations
        And the new product should have 1 common advantage
        And the new product should be without author

    Scenario: As contributor, I can duplicate a product with only his advice.
        Given I have only the advice group in the context
        When I duplicate the easybreath product
        Then a new product should have been created
        And the new product should have 0 category
        And the new product should have 1 advice
        And the new product should have 0 technical informations
        And the new product should have 0 common advantage
        And the new product should be without author

    Scenario: I cannot duplicate a product without group in my duplication context
        Given I have no group in the context
        When I duplicate the easybreath product
        Then I should have an exception for have no group in the context

    Scenario: I cannot duplicate a product with an unknown group in my duplication context
        Given I have an unknown group in the context
        When I duplicate the easybreath product
        Then I should have an exception for have an unknown group in the context
