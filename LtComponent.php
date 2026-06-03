<?php

namespace Apps\Tms\Components\System\Tools\Lt;

use Apps\Tms\Packages\Adminltetags\Traits\DynamicTable;
use Apps\Tms\Packages\System\Tools\Lt\SystemToolsLt;
use Apps\Tms\Packages\System\Tools\Uom\SystemToolsUom;
use System\Base\BaseComponent;

class LtComponent extends BaseComponent
{
    use DynamicTable;

    protected $ltPackage;

    public function initialize()
    {
        $this->ltPackage = $this->usePackage(SystemToolsLt::class);

        $this->uomPackage = $this->usePackage(SystemToolsUom::class);
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        $this->view->uoms = $uoms = $this->uomPackage->getAll()->systemtoolsuom;

        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $lt = $this->ltPackage->getById((int) $this->getData()['id']);

                if (!$lt) {
                    return $this->throwIdNotFound();
                }

                $this->view->lt = $lt;
            }

            $this->view->pick('lt/view');

            return;
        }

        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'edit'      => 'system/tools/lt'
                ]
            ];

        $replaceColumns =
            function ($dataArr) use ($uoms) {
                if ($dataArr && is_array($dataArr) && count($dataArr) > 0) {
                    foreach ($dataArr as &$data) {
                        if (isset($uoms[$data['uom']]['name'])) {
                            $data['uom'] = $uoms[$data['uom']]['name'];
                        }
                    }
                }

                return $dataArr;
            };

        $this->generateDTContent(
            $this->ltPackage,
            'system/tools/lt/view',
            null,
            ['name','capacity','uom'],
            true,
            ['name','capacity','uom'],
            $controlActions,
            [],
            $replaceColumns,
            'name'
        );

        $this->view->pick('lt/list');
    }

    /**
     * @acl(name=add)
     */
    public function addAction()
    {
        $this->requestIsPost();

        $this->ltPackage->addLt($this->postData());

        $this->addResponse(
            $this->ltPackage->packagesData->responseMessage,
            $this->ltPackage->packagesData->responseCode
        );
    }

    /**
     * @acl(name=update)
     */
    public function updateAction()
    {
        $this->requestIsPost();

        $this->ltPackage->updateLt($this->postData());

        $this->addResponse(
            $this->ltPackage->packagesData->responseMessage,
            $this->ltPackage->packagesData->responseCode
        );
    }

    /**
     * @acl(name=remove)
     */
    public function removeAction()
    {
        $this->requestIsPost();

        $this->ltPackage->removeLt($this->postData());

        $this->addResponse(
            $this->ltPackage->packagesData->responseMessage,
            $this->ltPackage->packagesData->responseCode
        );
    }
}