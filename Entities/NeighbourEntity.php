<?php

/**
 * This file is part of the Venne:CMS (https://github.com/Venne)
 *
 * Copyright (c) 2011, 2012 Josef Kříž (http://www.josef-kriz.cz)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace CollaboratorModule\Entities;

use Venne;
use Nette\Object;

/**
 * @author Josef Kříž <pepakriz@gmail.com>
 * @Entity(repositoryClass="\Venne\Doctrine\ORM\BaseRepository")
 * @Table(name="collaborationNeighbour")
 */
class NeighbourEntity extends Object implements \Venne\Doctrine\ORM\IEntity
{


	/**
	 * @var \CollaboratorModule\Entities\UserEntity
	 * @Id
	 * @ManyToOne(targetEntity="\CollaboratorModule\Entities\UserEntity")
	 * @JoinColumn(name="user_from", referencedColumnName="id")
	 */
	protected $user_from;

	/**
	 * @var \CollaboratorModule\Entities\UserEntity
	 * @Id
	 * @ManyToOne(targetEntity="\CollaboratorModule\Entities\UserEntity")
	 * @JoinColumn(name="user_to", referencedColumnName="id")
	 */
	protected $user_to;

	/** @Column(type="decimal", precision=10, scale=10) */
	protected $score;

	/** @Column(type="integer") */
	protected $sum;

	/** @Column(type="integer") */
	protected $n;

	function __construct($from, $to, $score, $sum, $n)
	{
		$this->user_from = $from;
		$this->user_to = $to;
		$this->score = $score;
		$this->sum = $sum;
		$this->n = $n;
	}

	public function generateScore()
	{
		if($this->n <= 1){
			$this->score = 1;
		}else{
			$this->score = 1 - ((6*$this->sum) / ($this->n * (pow($this->n, 2) - 1)));
		}
	}

	public function setN($n)
	{
		$this->n = $n;
	}

	public function getN()
	{
		return $this->n;
	}

	public function setScore($score)
	{
		$this->score = $score;
	}

	public function getScore()
	{
		return $this->score;
	}

	public function setSum($sum)
	{
		$this->sum = $sum;
	}

	public function getSum()
	{
		return $this->sum;
	}

	/**
	 * @param \CollaboratorModule\Entities\UserEntity $user_from
	 */
	public function setUserFrom($user_from)
	{
		$this->user_from = $user_from;
	}

	/**
	 * @return \CollaboratorModule\Entities\UserEntity
	 */
	public function getUserFrom()
	{
		return $this->user_from;
	}

	/**
	 * @param \CollaboratorModule\Entities\UserEntity $user_to
	 */
	public function setUserTo($user_to)
	{
		$this->user_to = $user_to;
	}

	/**
	 * @return \CollaboratorModule\Entities\UserEntity
	 */
	public function getUserTo()
	{
		return $this->user_to;
	}


}
