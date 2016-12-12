<?php

namespace MauticPlugin\ContactTagsApiBundle\Controller;

use Mautic\ApiBundle\Controller\CommonApiController;
use Mautic\CoreBundle\Helper\InputHelper;
use Mautic\LeadBundle\Entity\Tag;
use Mautic\LeadBundle\Model\LeadModel;
use Mautic\LeadBundle\Entity\Lead;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class IndexApiController extends CommonApiController
{

    public function initialize(FilterControllerEvent $event)
    {
        parent::initialize($event);
        $this->model            = $this->getModel('lead.lead');
    }

    public function removeTagAction($contactId)
    {
        $tagName = $this->request->request->get('tagName');
        /** @var LeadModel $leadModel */
        $leadModel = $this->model;

        /** @var Lead $entityLead */
        $entityLead = $leadModel->getRepository()->findOneBySlugs('idcliente', $contactId);

        if(!$tagName || $entityLead === null) {
            return $this->notFound();
        }

        /** @var Tag $tag */
        $tag = $leadModel->getTagRepository()->findOneBy(['tag' => $tagName]);

        if(!$tag) {
            return $this->notFound();
        }

        $entityLead->removeTag($tag);

        $leadModel->saveEntity($entityLead);
        $view = $this->view([$this->entityNameOne => $entityLead]);

        return $this->handleView($view);
    }

    public function newTagAction($tagName)
    {
        /** @var LeadModel $leadModel */
        $leadModel = $this->model;

        if(!empty(trim($tagName))) {
            $objTag = new Tag();
            $objTag->setTag(InputHelper::clean($tagName));
            $leadModel->getTagRepository()->saveEntity($objTag);

            return $this->handleView($this->view(['result' => true]));
        }

        return $this->notFound();
    }

    public function addTagAction($contactId)
    {
        $tagName = $this->request->request->get('tagName');
        /** @var LeadModel $leadModel */
        $leadModel = $this->model;

        /** @var Lead $entityLead */
        $entityLead = $leadModel->getEntity((int) $contactId);

        if(!$tagName || $entityLead === null) {
            return $this->notFound();
        }

        /** @var Tag $tag */
        $tag = $leadModel->getTagRepository()->findOneBy(['tag' => $tagName]);

        if(!$tag) {
            return $this->handleView($this->view(['error' => 'tag not found']));
        }

        $entityLead->addTag($tag);

        $leadModel->saveEntity($entityLead);
        $view = $this->view([$this->entityNameOne => $entityLead]);

        return $this->handleView($view);
    }
}