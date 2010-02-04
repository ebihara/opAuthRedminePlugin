<?php

/**
 * This file is part of the opAuthRedminePlugin package.
 * (c) 2010 Kousuke Ebihara (http://co3k.org/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * opAuthLoginFormRedmine represents a form to login by one's Redmine account.
 *
 * @package    opAuthRedminePlugin
 * @subpackage user
 * @author     Kousuke Ebihara <ebihara@tejimaya.com>
 */
class opAuthLoginFormRedmine extends opAuthLoginForm
{
  public function configure()
  {
    $this
      ->setWidget('redmine_username', new sfWidgetFormInput())
      ->setValidator('redmine_username', new opValidatorString())

      ->setWidget('password', new sfWidgetFormInputPassword())
      ->setValidator('password', new opValidatorString())
    ;

    $this->mergePostValidator(new sfValidatorCallback(array(
      'callback' => array($this, 'validate'),
    )));

    parent::configure();
  }

  public function validate($validator, $values, $arguments = array())
  {
    $conn = $this->getAuthAdapter()->getRedmineConnection();

    $sql = 'SELECT id FROM users WHERE login = ? AND hashed_password = ? AND status = ?';
    $isValid = (bool)$conn->fetchOne($sql, array($values['redmine_username'], sha1($values['password']), 1));
    if (!$isValid)
    {
      throw new sfValidatorError($validator, 'Failed to redmine authentication');
    }

    $validator = new opAuthValidatorMemberConfig(array('config_name' => 'redmine_username'));
    $result = $validator->clean($values);

    return $result;
  }
}