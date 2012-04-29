<?php

/**
 * This file is part of the Venne:CMS (https://github.com/Venne)
 *
 * Copyright (c) 2011, 2012 Josef Kříž (http://www.josef-kriz.cz)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace CollaboratorModule\Subscribers;

use Doctrine\Common\EventSubscriber;
use Venne\ContentExtension\Events;
use Nette\DI\Container;
use NavigationModule\Entities\NavigationEntity;

/**
 * @author Josef Kříž <pepakriz@gmail.com>
 */
class CollaboratorSubscriber implements EventSubscriber
{

	/** @var \Venne\Doctrine\ORM\BaseRepository */
	protected $repository;
	
	/** @var \Nette\Application\Application */
	protected $application;



	/**
	 * @param Container $context
	 */
	public function __construct(Container $context, \Nette\Application\Application $application)
	{
		$this->repository = $context->collaborator->collaborationRepository;
		$this->application = $application;
	}



	/**
	 * @return array
	 */
	public function getSubscribedEvents()
	{
		return array(
			Events::onContentExtensionCreate,
			Events::onContentExtensionLoad,
			Events::onContentExtensionSave,
			Events::onContentExtensionRender
		);
	}



	public function onContentExtensionSave(\Venne\ContentExtension\EventArgs $args)
	{
		$form = $args->form;
		$page = $args->page;

		if (!$form->entity->translationFor) {
			$values = $form->getContentExtensionContainer("collaborator")->getValues();

			$item = $page->id ? $this->repository->findOneBy(array("page" => $page->id,)) : NULL;

			if (!$item) {
				if ($values["use"] || $values["show"]) {
					$entity = $this->repository->createNew();
					$entity->page = $page;
					$entity->use = $values["use"];
					$entity->show = $values["show"];

					$this->repository->save($entity);
				}
			} else {
				if ($values["use"] || $values["show"]) {
					$entity = $this->repository->findOneBy(array("page" => $page->id));
					$entity->use = $values["use"];
					$entity->show = $values["show"];
					$this->repository->update($entity);
				} else {
					$entity = $this->repository->findOneBy(array("page" => $page->id));
					$this->repository->delete($entity);
				}
			}
		}
	}



	public function onContentExtensionCreate(\Venne\ContentExtension\EventArgs $args)
	{
		$form = $args->form;

		if (!$form->entity->translationFor) {
			$container = $form->addContentExtensionContainer("collaborator", "Collaborator settings");
			$container->addCheckbox("use", "Monitor this page for collaboration");
			$container->addCheckbox('show', 'Show collaborator extension');
			$form->setCurrentGroup();
		}
	}



	public function onContentExtensionLoad(\Venne\ContentExtension\EventArgs $args)
	{
		$form = $args->form;
		$page = $args->page;

		if (!$form->entity->translationFor) {
			$container = $form->getContentExtensionContainer("collaborator");

			$item = $this->repository->findOneBy(array("page" => $page->id));
			if ($item) {
				$container["use"]->setValue($item->use);
				$container["show"]->setValue($item->show);
			} else {
				$container["use"]->setValue(false);
				$container["show"]->setValue(false);
			}
		}
	}
	
	
	
	public function onContentExtensionRender()
	{
		$presenter = $this->application->getPresenter();

		$component = new \CollaboratorModule\Components\LoggerControl();
		$presenter->addComponent($component, "contentExtension_collaboratorLogger");
		$component->render();

		$component = new \CollaboratorModule\Components\RecommendationControl();
		$presenter->addComponent($component, "contentExtension_collaboratorRecommendation");
		$component->render();
	}

}
