<?php
namespace Dpn\DpnGlossary\Domain\Repository;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Daniel Dorndorf <dorndorf@dreipunktnull.com>, dreipunktnull
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use Dpn\DpnGlossary\Domain\Model\Term;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 *
 * @package dpn_glossary
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class TermRepository extends Repository {

	/**
	 * find all terms (respect sys_language_uid)
	 *
	 * @return array|QueryResultInterface
	 */
	public function findAll() {
		$query = $this->createQuery();
		$query->matching($query->equals('sys_language_uid', $GLOBALS['TSFE']->sys_language_uid));
		return $query->execute();
	}

	/**
	 * find all terms ordered by name
	 *
	 * @param string $orderField
	 *
	 * @return array|QueryResultInterface
	 */
	public function findAllOrderedByName($orderField = 'name') {
		$query = $this->createQuery();
		$query->matching($query->equals('sys_language_uid', $GLOBALS['TSFE']->sys_language_uid));
		$query->setOrderings(array($orderField => QueryInterface::ORDER_ASCENDING));
		return $query->execute();
	}

	/**
	 * find all terms ordered by name and grouped by first character
	 *
	 * @return array
	 */
	public function findAllGroupedByFirstCharacter() {
		$terms          = $this->findAllOrderedByName();
		$numbers        = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
		$normalChars    = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
		$sortedTerms    = array();
		foreach ($terms as $term) {
			/** @var Term $term */
			mb_internal_encoding("UTF-8");
			$firstCharacter = mb_strtolower(mb_substr($term->getName(), 0, 1));
			if (in_array($firstCharacter, $numbers)) {
				$firstCharacter = '0-9';
			} else if (!in_array($firstCharacter, $normalChars)) {
				switch ($firstCharacter) {
					case 'ä':
						$firstCharacter = 'a';
					break;
					case 'ö':
						$firstCharacter = 'o';
					break;
					case 'ü':
						$firstCharacter = 'u';
					break;
					default:
						$firstCharacter = '_';
					break;
				}
			}
			$firstCharacter = strtoupper($firstCharacter);
			if (!isset($sortedTerms[$firstCharacter])) {
				$sortedTerms[$firstCharacter] = array();
			}
			$sortedTerms[$firstCharacter][] = $term;
		}
		return $sortedTerms;
	}
}