Feature: Session access
  Scenario: Check data does not exist
    When data does not exist
    Then property check returns false
    And array access check returns false
  Scenario: Check data exist
    When data exists
    Then property check returns true
    And array access check returns true
  Scenario: Read data that exists
    When data exists
    Then property read returns data
    And array access read returns data
  Scenario: Read data that does not exist
    When data does not exist
    Then property read triggers error
    And property read returns null
    And array access read triggers error
    And array access read returns null
  Scenario: Null coalesce when data does not exist
    When data does not exist
    Then property read with null coalesce returns null
    And array access read with null coalesce returns null
  Scenario: Write property data
    When data does not exist
    Then property write succeeds
  Scenario: Write array access data
    When data does not exist
    Then array access write succeeds
