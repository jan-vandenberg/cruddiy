@reconfigure
Feature: Check public index page content

  Scenario: Check Brands list
    Given I am on "/core/app/brands-index.php"
    Then I should not see "Parse error"
    And I should not see "Fatal error"

    # Check results per page (2)
    And I should see "7 results - Page 1 of 4"
    And I should see "Under Armour"
    And I should see "Gola"
    And I should not see "Nike"
    And I should not see "Le Coq Sportif"
    And I should not see "Adidas"
    And I should not see "Reebok"
    And I should not see "Puma"

    # Last
    When I follow "Last"
    Then I should see "7 results - Page 4 of 4"
    And I should see "Puma"
    And I should not see "Under Armour"
    And I should not see "Gola"
    And I should not see "Nike"
    And I should not see "Le Coq Sportif"
    And I should not see "Adidas"
    And I should not see "Reebok"

    # First
    When I follow "First"
    And I follow "Next"
    Then I should see "7 results - Page 2 of 4"
    And I should see "Nike"
    And I should see "Le Coq Sportif"
    And I should not see "Under Armour"
    And I should not see "Gola"
    And I should not see "Adidas"
    And I should not see "Reebok"
    And I should not see "Puma"

    # Reset filters
    When I follow "Reset View"
    And I should see "7 results - Page 1 of 4"
    And I should see "Under Armour"
    And I should see "Gola"
    And I should not see "Nike"
    And I should not see "Le Coq Sportif"
    And I should not see "Adidas"
    And I should not see "Reebok"
    And I should not see "Puma"


  Scenario: Check Products list
    Given I am on "/core/app/brands-index.php"
    And I am on "/core/app/suppliers-index.php"
    Then I should see "No records were found."

