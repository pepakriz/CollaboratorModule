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
use Venne\Doctrine\ORM\BaseEntity;

/**
 * @author Josef Kříž <pepakriz@gmail.com>
 * @Entity(repositoryClass="\Venne\Doctrine\ORM\BaseRepository")
 * @Table(name="collaborationUser")
 */
class UserEntity extends BaseEntity
{


	/**
	 * @var \CoreModule\Entities\UserEntity
	 * @ManyToOne(targetEntity="\CoreModule\Entities\UserEntity")
	 * @JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	protected $user;

	/** @var \CollaboratorModule\Managers\CollaboratorManager */
	protected $collaborator;

	/**
	 * @OneToMany(targetEntity="NeighbourEntity", mappedBy="user_from", cascade={"persist"}, orphanRemoval=true)
	 * @OrderBy({"score" = "DESC"})
	 */
	protected $neighbours;

	/**
	 * @OneToMany(targetEntity="NeighbourEntity", mappedBy="user_to", cascade={"persist"}, orphanRemoval=true)
	 * @OrderBy({"score" = "DESC"})
	 */
	protected $neighboursFrom;

	/**
	 * @var array[]LogEntity
	 * @OneToMany(targetEntity="LogEntity", mappedBy="user")
	 */
	protected $tags;



	function __construct(\CoreModule\Entities\UserEntity $user)
	{
		$this->user = $user;

		$this->neighbours = new \Doctrine\Common\Collections\ArrayCollection();
		$this->tags = new \Doctrine\Common\Collections\ArrayCollection();
	}




	/**
	 * Get CMS user.
	 * 
	 * @return \CoreModule\Entities\UserEntity
	 */
	public function getUser()
	{
		return $this->user;
	}



	/**
	 * Add tag for user.
	 * 
	 * @param type $tag 
	 */
	public function addTag($tag, $score = 1)
	{
		if(!$this->collaborator){
			throw new \Nette\InvalidArgumentException("CollaboratorManager not exists in UserEntity, use setCollaborator");
		}

		$this->collaborator->addTag($this, $tag, $score);
	}



	/**
	 * Find neighbours of user.
	 * 
	 * @param type $count 
	 */
	public function findNeighbours()
	{
		if(!$this->collaborator){
			throw new \Nette\InvalidArgumentException("CollaboratorManager not exists in UserEntity, use setCollaborator");
		}

		return $this->collaborator->findNeighbours($this);
	}



	/**
	 * Find favorite tags.
	 * 
	 * @param type $count 
	 */
	public function findTags($count = 10)
	{
		$ret = array();
		$scores = $this->getScores();

		foreach($this->getNeighbours() as $entity){
			foreach($entity->getUserTo()->getScores() as $tag=>$score){
				if(isset($scores[$tag])){
					continue;
				}

				if(!isset($ret[$tag])){
					$ret[$tag] = 0;
				}

				$ret[$tag] += $score;
			}
		}
		asort($ret);
		return array_splice($ret, 0, $count);
	}



	/**
	 * @param \CollaboratorModule\Managers\CollaboratorManager $collaborator
	 */
	public function setCollaborator(\CollaboratorModule\Managers\CollaboratorManager $collaborator)
	{
		$this->collaborator = $collaborator;
	}



	/**
	 * @param \Doctrine\Common\Collections\ArrayCollection $neighbours
	 */
	public function setNeighbours($neighbours)
	{
		$this->neighbours = $neighbours;
	}



	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getNeighbours()
	{
		return $this->neighbours;
	}



	/**
	 * @param $tags
	 */
	public function setTags($tags)
	{
		$this->tags = $tags;
	}


	/**
	 * @return mixed
	 */
	public function getTags()
	{
		return $this->tags;
	}


	public function getScores()
	{
		$ret = array();

		foreach($this->tags as $entity){
			$ret[$entity->tag] = $entity->score;
		}

		return $ret;
	}

	public function setNeighboursFrom($neighboursFrom)
	{
		$this->neighboursFrom = $neighboursFrom;
	}

	public function getNeighboursFrom()
	{
		return $this->neighboursFrom;
	}


}
