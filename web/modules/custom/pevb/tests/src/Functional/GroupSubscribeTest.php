<?php

namespace Drupal\Tests\pevb\Functional;

use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;
use Drupal\Tests\BrowserTestBase;

/**
 * @group og_group_subscribe
 */
class GroupSubscribeTest extends BrowserTestBase {

  protected static $modules = [
    'node',
    'user',
    'og',
    'pluggable_entity_view_builder',
    'og_group_subscribe',
  ];

  protected $defaultTheme = 'stark';

  public function testSubscribeCtaAndFlow(): void {
    $type = NodeType::create([
      'type' => 'group',
      'name' => 'Group',
    ]);
    $type->save();
    \Drupal::service('og.group_type_manager')->addGroup('node', 'group');

    $group = Node::create([
      'type' => 'group',
      'title' => 'Chess Club',
      'status' => 1,
    ]);
    $group->save();

    $account = $this->drupalCreateUser(['access content']);
    $this->drupalLogin($account);

    $this->drupalGet($group->toUrl());
    $this->assertSession()->pageTextContains("Hi {$account->getDisplayName()},");
    $this->assertSession()->linkExists('click here');

    $this->clickLink('click here');
    $this->assertSession()->pageTextContains('You are now subscribed to Chess Club.');

    $this->drupalGet($group->toUrl());
    $this->assertSession()->linkNotExists('click here');
  }
}
