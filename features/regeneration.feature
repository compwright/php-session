Feature: Session ID Regeneration
  IDs may be regenerated for security.
  Existing session data is copied to the new generated ID.
  The old session ID may be deleted when the new ID is generated.

  Scenario Outline: Session ID is regenerated
    Given session <handler> stored at <location>
    And session is started and modified
    When session ID is regenerated, delete old session
    Then session ID should change
    And session data should be preserved
    And old session should not remain

    Examples:
      | handler   | location |
      | kodus     | A        |
      | scrapbook | B        |
      | redis     | 0        |
      | file      | C        |
