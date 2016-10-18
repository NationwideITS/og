<?php

namespace Drupal\Tests\og\Functional;

use Drupal\node\Entity\NodeType;
use Drupal\og\Og;
use Drupal\og\OgGroupAudienceHelper;
use Drupal\Tests\BrowserTestBase;

/**
 * Tests the field settings configuration form for the OG audience field.
 *
 * @group og
 * @coversDefaultClass \Drupal\og\Plugin\EntityReferenceSelection\OgSelection
 */
class OgSelectionConfigurationFormTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'field',
    'field_ui',
    'node',
    'og',
    'system',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    // Add node types.
    NodeType::create([
      'type' => 'non_group',
      'name' => 'non_group',
    ])->save();

    NodeType::create([
      'type' => 'group_type1',
      'name' => 'group_type1',
    ])->save();

    NodeType::create([
      'type' => 'group_type2',
      'name' => 'group_type2',
    ])->save();

    NodeType::create([
      'type' => 'group_content',
      'name' => 'group_content',
    ])->save();

    Og::addGroup('node', 'group_type1');
    Og::addGroup('node', 'group_type2');

    Og::createField(OgGroupAudienceHelper::DEFAULT_FIELD, 'node', 'group_content');
  }

  /**
   * Test if a group that uses a string as ID can be referenced.
   *
   * @covers ::buildConfigurationForm
   */
  public function testConfigurationForm() {
    $user = $this->drupalCreateUser(['administer content types', 'administer node fields']);
    $this->drupalLogin($user);

    $this->drupalGet('admin/structure/types/manage/group_content/fields');
    $this->assertSession()->statusCodeEquals(200);

    $this->clickLink('Edit field settings');
    $this->assertSession()->statusCodeEquals(200);

    $this->assertSession()->fieldExists('settings[handler_settings][target_bundles][group_type1]');
    $this->assertSession()->fieldExists('settings[handler_settings][target_bundles][group_type2]');

    // Assert non-group and group-content don't appear.
    $this->assertSession()->fieldNotExists('settings[handler_settings][target_bundles][non_group]');
    $this->assertSession()->fieldNotExists('settings[handler_settings][target_bundles][group_content]');
  }

}
