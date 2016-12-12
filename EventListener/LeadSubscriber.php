<?php


namespace MauticPlugin\ContactTagsApiBundle\EventListener;

use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\LeadBundle\Event\LeadEvent;
use Mautic\LeadBundle\LeadEvents;

class LeadSubscriber extends CommonSubscriber
{
    public static function getSubscribedEvents()
    {
        return [
            LeadEvents::LEAD_PRE_SAVE => ['onAddTagToLead', 0]
        ];
    }

    public function onAddTagToLead(LeadEvent $events)
    {
        $idCliente = $this->request->query->get('idCliente');

        if($idCliente) {
            return;
        }

        $changes = $events->getChanges();

        if(isset($changes['tags'])) {
            $objContact = $events->getLead();
            $fields = $objContact->getFields();

            if($fields['core']['idcliente']['value'] && $fields['core']['idvendedor']['value']) {
                $curl = curl_init('http://badmin.com/webhook-mautic/updateTags');
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, [
                    'idCliente' => $fields['core']['idcliente']['value'],
                    'idVendedor' => $fields['core']['idvendedor']['value'],
                    'tags' => json_encode($changes['tags'])
                ]);

                $result = curl_exec($curl);
                curl_close($curl);
            }
        }
    }
}