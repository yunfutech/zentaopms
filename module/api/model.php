<?php
/**
 * The model file of api module of ZenTaoCMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     api
 * @version     $Id$
 * @link        http://www.zentao.net
 */
class apiModel extends model
{
    /* Status. */
    const STATUS_DOING = 'doing';
    const STATUS_DONE = 'done';
    const STATUS_HIDDEN = 'hidden';

    /* Scope. */
    const SCOPE_QUERY = 'query';
    const SCOPE_FORM_DATA = 'formData';
    const SCOPE_PATH = 'path';
    const SCOPE_BODY = 'body';
    const SCOPE_HEADER = 'header';
    const SCOPE_COOKIE = 'cookie';

    /* Params. */
    const PARAMS_TYPE_CUSTOM = 'custom';

    /**
     * @param object $data
     * @access public
     * @return int
     */
    public function publishLib($data)
    {
        /* Get lib modules list. */
        $modules = $this->dao->select('*')->from(TABLE_MODULE)
            ->where('root')->eq((int)$data->lib)
            ->andWhere('type')->eq('api')
            ->andWhere('deleted')->eq(0)
            ->orderBy('grade desc, `order`')
            ->fetchAll();

        /* Get all api list. */
        $apis = $this->dao->select('id,version')->from(TABLE_API)
            ->where('lib')->eq($data->lib)
            ->andWhere('deleted')->eq(0)
            ->fetchAll();

        /* Get all struct list. */
        $structs = $this->dao->select('id,version')->from(TABLE_APISTRUCT)
            ->where('lib')->eq($data->lib)
            ->andWhere('deleted')->eq(0)
            ->fetchAll();

        $snap = array('modules' => $modules, 'apis' => $apis, 'structs' => $structs);

        $data->snap = json_encode($snap);
        $this->dao->insert(TABLE_API_LIB_RELEASE)->data($data)
            ->autoCheck()
            ->batchCheck($this->config->api->createrelease->requiredFields, 'notempty')
            ->exec();

        return dao::isError() ? false : $this->dao->lastInsertID();
    }

    /**
     * Delete a lib publish.
     *
     * @param int $id
     * @access public
     * @return void
     */
    public function deleteRelease($id)
    {
        $this->dao->delete()->from(TABLE_API_LIB_RELEASE)
            ->where('id')->eq($id)
            ->exec();
    }

    /**
     * Create an api doc.
     *
     * @param object $params
     * @access public
     * @return int
     */
    public function create($params)
    {
        $this->dao->insert(TABLE_API)->data($params)
            ->autoCheck()
            ->batchCheck($this->config->api->create->requiredFields, 'notempty')
            ->exec();

        $params->id = $this->dao->lastInsertID();

        $apiSpec = $this->getApiSpecByData($params);
        $this->dao->replace(TABLE_API_SPEC)->data($apiSpec)->exec();

        return dao::isError() ? false : $params->id;
    }

    /**
     * Create a global struct
     *
     * @param object $data
     * @access public
     * @return int
     */
    public function createStruct($data)
    {
        $data->version = 1;
        $this->dao->insert(TABLE_APISTRUCT)->data($data)
            ->autoCheck()
            ->batchCheck($this->config->api->struct->requiredFields, 'notempty')
            ->exec();

        $id = $this->dao->lastInsertID();

        /* Create a struct version. */
        $version = array(
            'name'      => $data->name,
            'type'      => $data->type,
            'desc'      => $data->desc,
            'version'   => $data->version,
            'attribute' => $data->attribute,
            'addedBy'   => $data->addedBy,
            'addedDate' => $data->addedDate
        );
        $this->dao->insert(TABLE_APISTRUCT_SPEC)->data($version)->exec();

        return dao::isError() ? 0 : $id;
    }

    /**
     * Update a struct.
     *
     * @param int $id
     * @param object $data
     * @access public
     * @return array
     */
    public function updateStruct($id, $data)
    {
        $old = $this->dao->findByID($id)->from(TABLE_APISTRUCT)->fetch();

        unset($data->addedBy);
        unset($data->addedDate);

        $data->version = $old->version + 1;

        $this->dao->update(TABLE_APISTRUCT)
            ->data($data)->autoCheck()
            ->batchCheck($this->config->api->struct->requiredFields, 'notempty')
            ->where('id')->eq($id)
            ->exec();

        if(dao::isError()) return false;

        /* Create a struct version */
        $version = array(
            'name'      => $data->name,
            'type'      => $data->type,
            'desc'      => $data->desc,
            'version'   => $data->version,
            'attribute' => $data->attribute,
            'addedBy'   => $data->editedBy,
            'addedDate' => $data->editedDate
        );
        $this->dao->insert(TABLE_APISTRUCT_SPEC)->data($version)->exec();

        return common::createChanges($old, $data);
    }

    /**
     * Delete a struct.
     *
     * @param  int $id
     * @access public
     * @return void
     */
    public function deleteStruct($id)
    {
        $this->dao->update(TABLE_APISTRUCT)
            ->set('deleted')->eq(1)
            ->where('id')->eq($id)
            ->exec();
    }

    /**
     * Update an api doc.
     *
     * @param  int $apiID
     * @access public
     * @return bool|array
     */
    public function update($apiID)
    {
        $oldApi = $this->dao->findByID($apiID)->from(TABLE_API)->fetch();

        if(!empty($_POST['editedDate']) and $oldApi->editedDate != $this->post->editedDate)
        {
            dao::$errors[] = $this->lang->error->editedByOther;
            return false;
        }

        $now     = helper::now();
        $account = $this->app->user->account;
        $data    = fixer::input('post')
            ->skipSpecial('params,response')
            ->add('editedBy', $account)
            ->add('editedDate', $now)
            ->add('version', $oldApi->version)
            ->setDefault('product,module', 0)
            ->remove('type')
            ->get();

        $changes = common::createChanges($oldApi, $data);
        if(!empty($changes)) $data->version = $oldApi->version + 1;

        $this->dao->update(TABLE_API)
            ->data($data)
            ->autoCheck()
            ->batchCheck($this->config->api->edit->requiredFields, 'notempty')
            ->where('id')->eq($apiID)
            ->exec();

        $data->id = $apiID;
        $apiSpec  = $this->getApiSpecByData($data);
        $this->dao->replace(TABLE_API_SPEC)->data($apiSpec)->exec();

        return $changes;
    }

    /**
     * Get struct list by api doc id.
     *
     * @param int $id
     * @access public
     * @return array
     */
    public function getStructListByLibID($id)
    {
        $res = $this->dao->select('*')
            ->from(TABLE_APISTRUCT)
            ->where('lib')->eq($id)
            ->fetchAll();

        array_map(function ($item) {
            $item->attribute = json_decode($item->attribute, true);
            return $item;
        }, $res);
        return $res;
    }

    /**
     * Get a struct info.
     *
     * @param int $id
     * @access public
     * @return object
     */
    public function getStructByID($id)
    {
        $model = $this->dao->select('*')
            ->from(TABLE_APISTRUCT)
            ->where('id')->eq($id)
            ->fetch();

        if($model) $model->attribute = json_decode($model->attribute, true);

        return $model;
    }

    /**
     * Get release by version.
     *
     * @param int $libID
     * @param string $version
     * @return object
     * @access public
     */
    public function getReleaseByVersion($libID, $version)
    {
        $model = $this->dao->select('*')
            ->from(TABLE_API_LIB_RELEASE)
            ->where('version')->eq($version)
            ->andWhere('lib')->eq($libID)
            ->fetch();
        if($model) $model->snap = json_decode($model->snap, true);
        return $model;
    }

    /**
     * Get release by id.
     *
     * @param int $id
     * @access public
     * @return array
     */
    public function getReleaseById($id)
    {
        $model = $this->dao->select('*')
            ->from(TABLE_API_LIB_RELEASE)
            ->where('id')->eq($id)
            ->fetch();
        if($model) $model->snap = json_decode($model->snap, true);
        return $model;
    }

    /**
     * Get Versions by api id
     *
     * @param int $libID
     * @access public
     * @return array
     */
    public function getReleaseListByApi($libID)
    {
        $versions = $this->dao->select('*')->from(TABLE_API_LIB_RELEASE)
            ->where('lib')->eq($libID)
            ->fetchAll('id');
        return $versions;
    }


    /**
     * Get api doc by id.
     *
     * @param int $id
     * @param int $version
     * @param int $release
     * @access public
     * @return object
     */
    public function getLibById($id, $version = 0, $release = 0)
    {
        if($release)
        {
            $rel = $this->getReleaseById($release);
            foreach($rel->snap['apis'] as $api)
            {
                if($api['id'] == $id) $version = $api['version'];
            }
        }
        if($version)
        {
            $fields = 'spec.*,api.id,api.product,api.lib,api.version,api.paramsExample,api.responseExample,doc.name as libName,module.name as moduleName,api.editedBy,api.editedDate';
        }
        else
        {
            $fields = 'api.*,doc.name as libName,module.name as moduleName';
        }

        $model = $this->dao
            ->select($fields)
            ->from(TABLE_API)->alias('api')
            ->beginIF($version)->leftJoin(TABLE_API_SPEC)->alias('spec')->on('api.id = spec.doc')->fi()
            ->leftJoin(TABLE_DOCLIB)->alias('doc')->on('api.lib = doc.id')
            ->leftJoin(TABLE_MODULE)->alias('module')->on('api.module = module.id')
            ->where('api.id')->eq($id)
            ->beginIF($version)->andWhere('spec.version')->eq($version)->fi()
            ->fetch();

        if($model)
        {
            $model->params   = json_decode($model->params, true);
            $model->response = json_decode($model->response, true);
        }
        return $model;
    }

    /**
     * Get api list by release.
     *
     * @param object $release
     * @param string $where
     * @return array
     */
    public function getApiListByRelease($release, $where = '')
    {
        $strJoin = array();
        foreach($release->snap['apis'] as $api)
        {
            $strJoin[] = "(spec.doc = {$api['id']} and spec.version = {$api['version']} )";
        }

        if($strJoin) $where .= 'and (' . implode(' or ', $strJoin) . ')';
        $list = $this->dao->select('api.lib,spec.*,api.id')->from(TABLE_API)->alias('api')
            ->leftJoin(TABLE_API_SPEC)->alias('spec')->on('api.id = spec.doc')
            ->where($where)
            ->fetchAll();
        return $list;
    }

    /**
     * Get api doc list by module id
     *
     * @param int $libID
     * @param int $moduleID
     * @param int $release
     * @return array $list
     * @author thanatos thanatos915@163.com
     */
    public function getListByModuleId($libID = 0, $moduleID = 0, $release = 0)
    {
        /* Get release info. */
        if($release > 0)
        {
            $rel = $this->getReleaseById($release);

            $where = "1=1 and lib = $libID ";
            if($moduleID > 0)
            {
                $sub = array();
                foreach($rel->snap['modules'] as $module)
                {
                    $tmp = explode(',', $module['path']);
                    if(in_array($moduleID, $tmp))
                    {
                        $sub[] = $module['id'];
                    }
                }
                if($sub) $where .= 'and module in (' . implode(',', $sub) . ')';
            }
            $list = $this->getApiListByRelease($rel, $where);
        }
        else
        {

            if($moduleID > 0)
            {
                $sub   = $this->dao->select('id')->from(TABLE_MODULE)->where('FIND_IN_SET(' . $moduleID . ', path)')->processSQL();
                $where = 'module in (' . $sub . ')';
            }
            else
            {
                $where = 'lib = ' . $libID;
            }
            $list = $this->dao->select('*')
                ->from(TABLE_API)
                ->where($where)
                ->andWhere('deleted')->eq(0)
                ->fetchAll();
        }
        array_map(function ($item) {
            $item->params   = json_decode($item->params, true);
            $item->response = json_decode($item->response, true);
            return $item;
        }, $list);
        return $list;
    }

    /**
     * Get status text by status.
     *
     * @param string $status
     * @access public
     * @return string
     */
    public static function getApiStatusText($status)
    {
        global $lang;
        switch($status)
        {
            case static::STATUS_DOING:
            {
                return $lang->api->doing;
            }
            case static::STATUS_DONE:
            {
                return $lang->api->done;
            }
        }
    }

    /**
     * @param int $libID
     * @param string $pager
     * @param string $orderBy
     * @access public
     * @return array
     */
    public function getStructByQuery($libID, $pager = '', $orderBy = '')
    {
        return $this->dao->select('s.*,user.realname as addedName')->from(TABLE_APISTRUCT)->alias('s')
            ->leftJoin(TABLE_USER)->alias('user')->on('user.account = s.addedBy')
            ->where('s.deleted')->eq(0)
            ->andWhere('s.lib')->eq($libID)
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();
    }

    /**
     * @param int $libID
     * @param string $pager
     * @param string $orderBy
     * @access public
     * @return array
     */
    public function getReleaseByQuery($libID, $pager = '', $orderBy = '')
    {
        return $this->dao->select('*')->from(TABLE_API_LIB_RELEASE)
            ->where('lib')->eq($libID)
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();
    }

    /**
     * Get struct tree by lib id
     *
     * @param int $libID
     * @param int $structID
     * @access public
     * @return string
     */
    public function getStructTreeByLib($libID = 0, $structID = 0)
    {
        $list = $this->getStructListByLibID($libID);

        $html = "<ul id='modules' class='tree' data-ride='tree' data-name='tree-lib'>";
        foreach($list as $item)
        {
            $class = array('catalog');
            if($structID && $structID == $item->id)
            {
                $class[] = 'active';
            }
            else
            {
                $class[] = 'doc';
            }

            $html .= '<li class="' . implode(' ', $class) . '">';
            $html .= html::a(helper::createLink('api', 'struct', "libID=$libID&structID=$item->id"), "<i class='icon icon-file-text text-muted'></i> &nbsp;" . $item->name, '', "data-app='{$this->app->tab}' class='doc-title' title='{$item->name}'");
            $html .= "</li>";
        }
        $html .= "</ul>";

        return $html;
    }

    /**
     * Get the details of the method by file path.
     *
     * @param string $filePath
     * @param string $ext
     * @access public
     * @return object
     */
    public function getMethod($filePath, $ext = '')
    {
        $fileName  = dirname($filePath);
        $className = basename(dirname(dirname($filePath)));
        if(!class_exists($className)) helper::import($fileName);
        $methodName = basename($filePath);

        $method           = new ReflectionMethod($className . $ext, $methodName);
        $data             = new stdClass();
        $data->startLine  = $method->getStartLine();
        $data->endLine    = $method->getEndLine();
        $data->comment    = $method->getDocComment();
        $data->parameters = $method->getParameters();
        $data->className  = $className;
        $data->methodName = $methodName;
        $data->fileName   = $fileName;
        $data->post       = false;

        $file = file($fileName);
        for($i = $data->startLine - 1; $i <= $data->endLine; $i++)
        {
            if(strpos($file[$i], '$this->post') or strpos($file[$i], 'fixer::input') or strpos($file[$i], '$_POST'))
            {
                $data->post = true;
            }
        }
        return $data;
    }

    /**
     * Request the api.
     *
     * @param string $moduleName
     * @param string $methodName
     * @param string $action
     * @access public
     * @return array
     */
    public function request($moduleName, $methodName, $action)
    {
        $host  = common::getSysURL();
        $param = '';
        if($action == 'extendModel')
        {
            if(!isset($_POST['noparam']))
            {
                foreach($_POST as $key => $value) $param .= ',' . $key . '=' . $value;
                $param = ltrim($param, ',');
            }
            $url = rtrim($host, '/') . inlink('getModel', "moduleName=$moduleName&methodName=$methodName&params=$param", 'json');
            $url .= $this->config->requestType == "PATH_INFO" ? '?' : '&';
            $url .= $this->config->sessionVar . '=' . session_id();
        }
        else
        {
            if(!isset($_POST['noparam']))
            {
                foreach($_POST as $key => $value) $param .= '&' . $key . '=' . $value;
                $param = ltrim($param, '&');
            }
            $url = rtrim($host, '/') . helper::createLink($moduleName, $methodName, $param, 'json');
            $url .= $this->config->requestType == "PATH_INFO" ? '?' : '&';
            $url .= $this->config->sessionVar . '=' . session_id();
        }

        /* Unlock session. After new request, restart session. */
        session_write_close();
        $content = file_get_contents($url);
        session_start();

        return array('url' => $url, 'content' => $content);
    }

    /**
     * Query sql.
     *
     * @param string $sql
     * @param string $keyField
     * @access public
     * @return array
     */
    public function sql($sql, $keyField = '')
    {
        if(!$this->config->features->apiSQL) return sprintf($this->lang->api->error->disabled, '$config->features->apiSQL');

        $sql = trim($sql);
        if(strpos($sql, ';') !== false) $sql = substr($sql, 0, strpos($sql, ';'));

        $result            = array();
        $result['status']  = 'fail';
        $result['message'] = '';

        if(empty($sql)) return $result;

        if(stripos($sql, 'select ') !== 0)
        {
            $result['message'] = $this->lang->api->error->onlySelect;
            return $result;
        }

        try
        {
            $stmt = $this->dbh->query($sql);

            $rows = array();
            if(empty($keyField))
            {
                $rows = $stmt->fetchAll();
            }
            else
            {
                while($row = $stmt->fetch()) $rows[$row->$keyField] = $row;
            }

            $result['status'] = 'success';
            $result['data']   = $rows;
        }
        catch(PDOException $e)
        {
            $result['status']  = 'fail';
            $result['message'] = $e->getMessage();
        }

        return $result;
    }

    /**
     * Get spec of api.
     *
     * @param object $data
     * @access private
     * @return array
     */
    private function getApiSpecByData($data)
    {
        return array(
            'doc'          => $data->id,
            'module'       => $data->module,
            'title'        => $data->title,
            'path'         => $data->path,
            'protocol'     => $data->protocol,
            'method'       => $data->method,
            'requestType'  => $data->requestType,
            'responseType' => isset($data->responseType) ? $data->responseType : '',
            'status'       => $data->status,
            'owner'        => $data->owner,
            'desc'         => $data->desc,
            'version'      => $data->version,
            'params'       => $data->params,
            'response'     => $data->response,
            'addedBy'      => $this->app->user->account,
            'addedDate'    => helper::now(),
        );
    }

    /**
     * Get Type list.
     *
     * @param  int   $libID
     * @access public
     * @return void
     */
    public function getTypeList($libID)
    {
        $typeList = array();
        foreach($this->lang->api->paramsTypeOptions as $key => $item)
        {
            $typeList[$key] = $item;
        }

        /* Get all struct by libID. */
        $structs = $this->getStructListByLibID($libID);
        foreach($structs as $struct)
        {
            $typeList[$struct->id] = $struct->name;
        }

        return $typeList;
    }

    /**
     * Create demo data.
     *
     * @param  string  $name
     * @param  string  $baseUrl
     * @param  string  $version
     * @access public
     * @return int
     */
    public function createDemoData($name, $baseUrl, $version = '16.0')
    {
        $firstAccount   = $this->dao->select('account')->from(TABLE_USER)->orderBy('id_asc')->limit(1)->fetch('account');
        $currentAccount = isset($this->app->user->account) ? $this->app->user->account : $firstAccount;

        /* Insert doclib. */
        $lib = new stdclass();
        $lib->type    = 'api';
        $lib->name    = $name;
        $lib->baseUrl = $baseUrl;
        $lib->acl     = 'open';
        $lib->users   = ',' . $currentAccount . ',';
        $this->dao->insert(TABLE_DOCLIB)->data($lib)->exec();

        $libID = $this->dao->lastInsertID();

        /* Insert struct. */
        $structMap = array();
        $structs   = $this->getDemoData('apistruct', $version);
        foreach($structs as $struct)
        {
            $oldID = $struct->id;
            unset($struct->id);

            $struct->lib        = $libID;
            $struct->addedBy    = $currentAccount;
            $struct->addedDate  = helper::now();
            $struct->editedBy   = $currentAccount;
            $struct->editedDate = helper::now();

            $this->dao->insert(TABLE_APISTRUCT)->data($struct)->exec();
            $newID = $this->dao->lastInsertID();

            $structMap[$oldID] = $newID;
        }

        /* Insert struct spec. */
        $specs = $this->getDemoData('apistruct_spec', $version);
        foreach($specs as $spec)
        {
            unset($spec->id);

            $spec->addedBy   = $currentAccount;
            $spec->addedDate = helper::now();

            $this->dao->insert(TABLE_APISTRUCT_SPEC)->data($spec)->exec();
        }

        /* Insert module. */
        $modules = $this->getDemoData('module', $version);
        foreach($modules as $module)
        {
            if($module->type != 'api') continue;

            $oldID = $module->id;
            unset($module->id);

            $module->root = $libID;

            $this->dao->insert(TABLE_MODULE)->data($module)->exec();
            $newID = $this->dao->lastInsertID();
            $this->dao->update(TABLE_MODULE)->set('path')->eq(",$newID,")->where('id')->eq($newID)->exec();

            $moduleMap[$oldID] = $newID;
        }

        /* Insert api. */
        $this->loadModel('action');
        $apiMap = array();
        $apis   = $this->getDemoData('api', $version);
        foreach($apis as $api)
        {
            $oldID = $api->id;
            unset($api->id);

            $api->lib        = $libID;
            $api->module     = $moduleMap[$api->module];
            $api->addedBy    = $currentAccount;
            $api->addedDate  = helper::now();
            $api->editedBy   = $currentAccount;
            $api->editedDate = helper::now();

            $this->dao->insert(TABLE_API)->data($api)->exec();
            $newID = $this->dao->lastInsertID();

            $this->action->create('api', $newID, 'Created', '', '', $currentAccount);

            $apiMap[$oldID] = $newID;
        }

        /* Insert api spec. */
        $specs = $this->getDemoData('apispec', $version);
        foreach($specs as $spec)
        {
            unset($spec->id);

            $spec->doc       = $apiMap[$spec->doc];
            $spec->module    = zget($moduleMap, $spec->module, 0);
            $spec->owner     = $currentAccount;
            $spec->addedBy   = $currentAccount;
            $spec->addedDate = helper::now();

            $this->dao->insert(TABLE_API_SPEC)->data($spec)->exec();
        }

        return $libID;
    }

    /**
     * Get demo data.
     *
     * @param  string   $table
     * @param  stirng   $version
     * @access private
     * @return array
     */
    private function getDemoData($table, $version)
    {
        $file = $this->app->getAppRoot() . 'db' . DS . 'api' . DS . $version . DS . $table;
        return unserialize(file_get_contents($file));
    }
}
