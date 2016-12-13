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
        $route = $this->request->get('_route');
        $host = $this->request->getHost();

        if($host !== 'gl.bannet.com.br') {
            return false;
        }

        //se a requisicao veio do app
        if($route == 'mautic_api_addtag' || $route == 'mautic_api_removetag') {
            return false;
        }

        $changes = $events->getChanges();

        if(isset($changes['tags'])) {
            $objContact = $events->getLead();
            $fields = $objContact->getFields();

            if($fields['core']['idcliente']['value'] && $fields['core']['idvendedor']['value']) {
                $curl = curl_init('http://app.bannet.com.br/webhook-mautic/updateTags');
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