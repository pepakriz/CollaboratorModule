<?php

/**
 * This file is part of the Venne:CMS (https://github.com/Venne)
 *
 * Copyright (c) 2011, 2012 Josef Kříž (http://www.josef-kriz.cz)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace CollaboratorModule\Components;

use Venne;
use Venne\Application\UI\Control;

/**
 * @author Josef Kříž <pepakriz@gmail.com>
 */
class LoggerControl extends Control
{

	/** @var \CommentsModule\Entities\CommentsEntity */
	protected $item;

	/** @var \CollaboratorModule\Entities\UserEntity */
	protected $user;


	public function startup()
	{
		parent::startup();

		if($this->getPresenter()->getUser()->isLoggedIn()){
			$repository = $this->presenter->context->collaborator->collaborationRepository;
			/** @var $userRepository \Venne\Doctrine\ORM\BaseRepository */
			$userRepository = $this->presenter->context->collaborator->userRepository;
			$cmsUserRepository = $this->presenter->context->core->userRepository;

			if ($this->presenter instanceof \CoreModule\Presenters\PagePresenter) {
				$this->item = $item = $repository->findOneBy(array("page" => $this->presenter->page->page->id));
				if ($item && $item->use) {
					$user = $this->getPresenter()->getContext()->user;

					$this->user = $userRepository->findOneByUser($user->identity->id);
					if(!$this->user){
						$cmsUser = $cmsUserRepository->find($user->identity->id);


						$this->user = $userRepository->createNew(array($cmsUser));
						$userRepository->save($this->user);
					}

					$this->user->setCollaborator($this->getPresenter()->getContext()->collaborator->collaboratorManager);

					$this->log();
				}
			}
		}
	}


	protected function log()
	{
		$this->user->addTag($this->getPresenter()->page->page->url);
	}



	public function render()
	{

	}


}
