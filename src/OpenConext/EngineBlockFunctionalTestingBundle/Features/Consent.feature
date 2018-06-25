@consent
Feature:
  In order to make an informed decision about what information I'm sharing with SPs
  As a user
  I want to send see what information the SP requires

  Background:
    Given an EngineBlock instance on "vm.openconext.org"
      And an Identity Provider named "Dummy-IdP"
      And a Service Provider named "Dummy-SP"
      And SP "Dummy-SP" allows the following attributes:

        | Name                                                    | Value | Source | Motivation                 |
        | urn:mace:dir:attribute-def:uid                          | *     |        | Motivation for uid         |
        | urn:mace:terena.org:attribute-def:schacHomeOrganization | *     |        | Motivation for sho         |
        | urn:mace:dir:attribute-def:cn                           | *     |        | Motivation for cn          |
        | urn:mace:dir:attribute-def:displayName                  | *     |        | Motivation for dn          |
        | urn:mace:dir:attribute-def:eduPersonAffiliation         | *     |        | Motivation for affiliation |
        | urn:mace:dir:attribute-def:eduPersonOrcid               | *     | voot   | Motivation for orcid       |

      And the IdP "Dummy-IdP" sends attribute "urn:mace:dir:attribute-def:cn" with value "test"
      And the IdP "Dummy-IdP" sends attribute "urn:mace:dir:attribute-def:displayName" with value "test"
      And the IdP "Dummy-IdP" sends attribute "urn:mace:dir:attribute-def:eduPersonAffiliation" with value "test"

      And SP "Dummy-SP" requires attribute aggregation
      And feature "eb.run_all_manipulations_prior_to_consent" is disabled
      And the attribute aggregator returns the attributes:

        | Name                                      |  Value | Source |
        | urn:mace:dir:attribute-def:eduPersonOrcid | 123456 | voot   |

  # The default behaviour of EB should be to ask for default consent.
  Scenario: The user is asked for consent to share information with the SP
    Given I log in at "Dummy-SP"
      And I pass through EngineBlock
      And I pass through the IdP
     Then the response should contain "Do you agree with sharing this data?"
     Then the response should contain "Yes, proceed to Dummy-SP"
     Then the response should contain "No, I do not agree"
     Then the response should contain "Dummy-SP needs your information before logging in"
     Then the response should contain "support@openconext.org"
     Then the response should contain "+31612345678"
     When I give my consent
     Then I pass through EngineBlock

  Scenario: The user is not asked for consent when disabled for a SP
    Given I log in at "Dummy-SP"
    And the IdP "Dummy-IdP" requires no consent for SP "Dummy-SP"
    And I pass through EngineBlock
    And I pass through the IdP
    And I pass through EngineBlock
    Then the url should match "functional-testing/Dummy-SP/acs"

  Scenario: The user is asked for minimal consent
    Given I log in at "Dummy-SP"
    And the IdP "Dummy-IdP" requires minimal consent for SP "Dummy-SP"
    And I pass through EngineBlock
    And I pass through the IdP
    Then the response should not contain "Do you agree with sharing this data?"
    Then the response should not contain "Yes, proceed to Dummy-SP"
    Then the response should contain "Proceed to Dummy-SP"
    Then the response should not contain "No, I do not agree"
    Then the response should contain "Cancel"

  Scenario: The user is asked for default consent
    Given I log in at "Dummy-SP"
    And the IdP "Dummy-IdP" requires default consent for SP "Dummy-SP"
    And I pass through EngineBlock
    And I pass through the IdP
    Then the response should contain "Do you agree with sharing this data?"
    Then the response should contain "Yes, proceed to Dummy-SP"
    Then the response should contain "No, I do not agree"
    Then the response should not contain "Cancel"

  Scenario: The user is can read why the service providers requires an attribute
    Given I log in at "Dummy-SP"
    And I pass through EngineBlock
    And I pass through the IdP
    Then the response should contain "Motivation for cn"
    Then the response should contain "Motivation for dn"
    Then the response should contain "Motivation for affiliation"
    Then the response should contain "Motivation for orcid"

  Scenario: The user presented with an institution provided consent text
    Given I log in at "Dummy-SP"
    And the IdP "Dummy-IdP" provides a consent message "Institutional privacy message" for SP "Dummy-SP"
    And I pass through EngineBlock
    And I pass through the IdP
    Then the response should contain "Institutional privacy message"
