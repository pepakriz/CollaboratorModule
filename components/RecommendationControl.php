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
class RecommendationControl extends Control
{

	/** @var \CommentsModule\Entities\CommentsEntity */
	protected $item;



	public function startup()
	{
		parent::startup();

		if($this->getPresenter()->getUser()->isLoggedIn()){
			$repository = $this->presenter->context->collaborator->collaborationRepository;
			$userRepository = $this->presenter->context->collaborator->userRepository;

			if ($this->presenter instanceof \CoreModule\Presenters\PagePresenter) {
				$this->item = $item = $repository->findOneBy(array("page" => $this->presenter->page->page->id));
				if ($item && $item->show) {
					$this->template->show = true;

					/** @var $user \CollaboratorModule\Entities\UserEntity */
					$user = $userRepository->findOneByUser($this->getPresenter()->getUser()->getIdentity()->id);
					if($user){
						$this->template->items = $user->findTags();
					}
				}
			}
		}
	}



}
