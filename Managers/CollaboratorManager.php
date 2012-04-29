<?php

/**
 * This file is part of the Venne:CMS (https://github.com/Venne)
 *
 * Copyright (c) 2011, 2012 Josef Kříž (http://www.josef-kriz.cz)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace CollaboratorModule\Managers;

use Venne;
use Nette\Object;
use Venne\Doctrine\ORM\BaseRepository;
use CollaboratorModule\Entities\UserEntity;

/**
 * @author Josef Kříž <pepakriz@gmail.com>
 */
class CollaboratorManager extends Object
{


	/** @var BaseRepository */
	protected $userRepository;

	/** @var BaseRepository */
	protected $logRepository;

	/** @var int */
	protected $history;

	/** @var int */
	protected $sumOfNeighbour;

	function __construct(\Nette\DI\Container $container)
	{
		$this->userRepository = $container->collaborator->userRepository;
		$this->logRepository = $container->collaborator->logRepository;
		$this->history = $container->parameters["modules"]["collaborator"]["history"];
		$this->sumOfNeighbour = $container->parameters["modules"]["collaborator"]["friends"];
	}



	/**
	 * Add tag for user.
	 *
	 * @param UserEntity $user
	 * @param type $tag
	 */
	public function addTag(UserEntity $user, $tag, $score = 1)
	{
		$this->cleanOldTags();

			$entity = $this->logRepository->findOneBy(array(
				"user" => $user->id,
				"tag" => $tag,
			));

			if(!$entity){
				$entity = new \CollaboratorModule\Entities\LogEntity($user, $tag, $score);
			}else{
				$this->removeNeighbourScore($user, $tag);
				$entity->updateDate();
				$entity->setScore($score);
			}
			$this->addNeighbourScore($user, $tag, $score);
			$this->logRepository->save($entity);
	}


	/**
	 * @param $user
	 * @param $tag
	 * @param $score
	 */
	protected function addNeighbourScore($user, $tag, $score)
	{
		foreach($user->getNeighbours() as $item){
			$neighbourScore = $item->getUserTo()->getScores();

			$item->n++;
			$item->sum += pow($score - (isset($neighbourScore[$tag]) ? $neighbourScore[$tag] : 0), 2);
			$item->generateScore();
		}

		foreach($user->getNeighboursFrom() as $item){
			$neighbourScore = $item->getUserFrom()->getScores();

			$item->n++;
			$item->sum += pow($score - (isset($neighbourScore[$tag]) ? $neighbourScore[$tag] : 0), 2);
			$item->generateScore();
		}
	}


	/**
	 * @param $user
	 * @param $tag
	 */
	protected function removeNeighbourScore($user, $tag)
	{
		foreach($user->getNeighbours() as $item){
			$scores = $user->getScores();
			$neighbourScore = $item->getUserTo()->getScores();

			$item->n--;
			$item->sum -= pow($scores[$tag] - (isset($neighbourScore[$tag]) ? $neighbourScore[$tag] : 0), 2);
			$item->generateScore();
		}

		foreach($user->getNeighboursFrom() as $item){
			$scores = $user->getScores();
			$neighbourScore = $item->getUserFrom()->getScores();

			$item->n--;
			$item->sum -= pow($scores[$tag] - (isset($neighbourScore[$tag]) ? $neighbourScore[$tag] : 0), 2);
			$item->generateScore();
		}
	}


	/**
	 * Find neighbours of user.
	 *
	 * @param UserEntity $user
	 * @param type $count
	 */
	public function findNeighbours(UserEntity $user)
	{
		$scores = $user->getScores();
		$tags = array_keys($scores);
		$neighbours = array();

		/** @var $qb \Doctrine\ORM\QueryBuilder */
		$qb = $this->userRepository->createQueryBuilder("a");
		$qb
			->join("a.tags", "b")
			->where($qb->expr()->in("b.tag", $tags))
			->andWhere("a.id != :id")
			->setParameters(array(
			"id"=>$user->id,
		));

		foreach($qb->getQuery()->getResult() as $entity){
			$sum = 0;
			$all = 0;

			$neighbourScores = $entity->getScores();
			$allScores = $scores + $neighbourScores;
			$n = count($allScores);

			foreach($allScores as $tag=>$item){
				 $one = isset($scores[$tag]) ? $scores[$tag] : 0;
				 $two = isset($neighbourScores[$tag]) ? $neighbourScores[$tag] : 0;
				 $sum += pow($one - $two, 2);
			}

			if($n <= 1){
				$all = 1;
			}else{
				$all = 1 - ((6*$sum) / ($n * (pow($n, 2) - 1)));
			}

			$neighbours[$all][$entity->id] = array("sum"=>$sum, "n"=>$n, "score"=>$all, "entity"=>$entity);
		}

		ksort($neighbours);
		$neighbours = array_reverse($neighbours);

		$user->getNeighbours()->clear();
		$this->userRepository->save($user);
		$i = 0;
		foreach($neighbours as $items){
			foreach($items as $item){
				if($i++ >= $this->sumOfNeighbour){
					break;
				}
				$user->neighbours[] = new \CollaboratorModule\Entities\NeighbourEntity($user, $item["entity"], $item["score"], $item["sum"], $item["n"]);
			}
		}

		$this->userRepository->save($user);
	}



	protected function cleanOldTags()
	{
		$date = new \DateTime();
		$date->sub(new \DateInterval('P'.$this->history.'D'));

		/** @var $qb \Doctrine\ORM\QueryBuilder */
		$qb = $this->logRepository->createQueryBuilder("a");
		$qb->where("a.last <  :date")->setParameter("date", $date);

		foreach($qb->getQuery()->getResult() as $entity){
			$this->logRepository->delete($entity);
		}
	}


}

