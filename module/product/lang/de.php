<?php
/**
 * The product module English file of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     product
 * @version     $Id: en.php 5091 2013-07-10 06:06:46Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
$lang->product->index         = $lang->productCommon . 'Home';
$lang->product->browse        = 'Story Liste';
$lang->product->dynamic       = 'Verlauf';
$lang->product->view          = 'Übersicht';
$lang->product->edit          = "{$lang->productCommon} bearbeiten";
$lang->product->batchEdit     = 'Mehrere bearbeiten';
$lang->product->create        = "Erstelle {$lang->productCommon}";
$lang->product->delete        = "Lösche {$lang->productCommon}";
$lang->product->deleted       = 'Gelöscht';
$lang->product->close         = 'Schließen';
$lang->product->closeAction   = "Close {$lang->productCommon}";
$lang->product->select        = "Auswahl {$lang->productCommon}";
$lang->product->mine          = 'Meine Zuständigkeiten';
$lang->product->other         = 'Andere';
$lang->product->closed        = 'Geschlossen';
$lang->product->updateOrder   = 'Ranking';
$lang->product->orderAction   = "Rank {$lang->productCommon}";
$lang->product->all           = "{$lang->productCommon} List";
$lang->product->manageLine    = "Manage {$lang->productCommon} Line";
$lang->product->newLine       = "Create {$lang->productCommon} Line";
$lang->product->export        = 'Exportiere Daten';
$lang->product->exportAction  = "Export {$lang->productCommon}";
$lang->product->dashboard     = 'Dashboard';
$lang->product->changeProgram = "{$lang->productCommon} confirmation of the scope of influence of adjustment of the program set";

$lang->product->basicInfo = 'Basis Info';
$lang->product->otherInfo = 'Andere Info';

$lang->product->plans       = 'Plan';
$lang->product->releases    = 'Release';
$lang->product->docs        = 'Dok';
$lang->product->bugs        = 'Verknüpfte Bugs';
$lang->product->projects    = "Linked Project";
$lang->product->executions  = "Verknüpfte {$lang->execution->common}";
$lang->product->cases       = 'Fälle';
$lang->product->builds      = 'Builds';
$lang->product->roadmap     = 'Roadmap';
$lang->product->doc         = 'Dok';
$lang->product->project     = $lang->executionCommon . 'Liste';
$lang->product->build       = 'Build';
$lang->product->moreProduct = "More Product";
$lang->product->projectInfo = "Projects that are linked to this {$lang->productCommon} are listed below.";

$lang->product->currentExecution      = "Aktuelle Execution";
$lang->product->activeStories         = 'Aktivierte [S]';
$lang->product->activeStoriesTitle    = 'Active Stories';
$lang->product->changedStories        = 'Geänderte [S]';
$lang->product->changedStoriesTitle   = 'Changed Stories';
$lang->product->draftStories          = 'Entwurf [S]';
$lang->product->draftStoriesTitle     = 'Draft Stories';
$lang->product->closedStories         = 'Geschlossene [S]';
$lang->product->closedStoriesTitle    = 'Closed Stories';
$lang->product->storyCompleteRate     = "{$lang->SRCommon} Completion rate";
$lang->product->activeRequirements    = "Active {$lang->URCommon}";
$lang->product->changedRequirements   = "Changed {$lang->URCommon}";
$lang->product->draftRequirements     = "Draft {$lang->URCommon}";
$lang->product->closedRequirements    = "Closed {$lang->URCommon}";
$lang->product->requireCompleteRate   = "{$lang->URCommon} Completion rate";
$lang->product->unResolvedBugs        = 'Ungelöste [B]';
$lang->product->unResolvedBugsTitle   = 'Active Bugs';
$lang->product->assignToNullBugs      = 'Nicht zugewiesene [B]';
$lang->product->assignToNullBugsTitle = 'Unassigned Bugs';
$lang->product->closedBugs            = 'Closed Bug';
$lang->product->bugFixedRate          = 'Bug Repair rate';

$lang->product->confirmDelete        = " Möchten Sie {$lang->productCommon} löschen?";
$lang->product->errorNoProduct       = "Kein {$lang->productCommon} erstellt!";
$lang->product->accessDenied         = "Sie haben keinen Zugriff auf {$lang->productCommon}.";
$lang->product->programChangeTip     = "The projects linked with this {$lang->productCommon}: %s will be transferred to the modified program set together.";
$lang->product->notChangeProgramTip  = "The {$lang->SRCommon} of {$lang->productCommon} has been linked to the following projects, please cancel the link before proceeding";
$lang->product->confirmChangeProgram = "The projects linked with this {$lang->productCommon}: %s is also linked with other products, whether to transfer projects to the modified program set.";
$lang->product->changeProgramError   = "The {$lang->SRCommon} of this {$lang->productCommon} has been linked to the project, please unlink it before proceeding";

$lang->product->id             = 'ID';
$lang->product->program        = "Program";
$lang->product->name           = 'Name';
$lang->product->code           = 'Alias';
$lang->product->line           = "{$lang->productCommon} Line";
$lang->product->lineName       = "{$lang->productCommon} Line Name";
$lang->product->order          = 'Sortierung';
$lang->product->type           = 'Typ';
$lang->product->typeAB         = 'Typ';
$lang->product->status         = 'Status';
$lang->product->subStatus      = 'Sub Status';
$lang->product->desc           = 'Beschreibung';
$lang->product->manager        = 'Manager';
$lang->product->PO             = 'PO';
$lang->product->QD             = 'QS Manager';
$lang->product->RD             = 'Release Manager';
$lang->product->acl            = 'Zugriffskontrolle';
$lang->product->whitelist      = 'Whitelist';
$lang->product->addWhitelist   = 'Add Whitelist';
$lang->product->unbindWhitelist = 'Remove Whitelist';
$lang->product->branch         = '%s';
$lang->product->qa             = 'QA';
$lang->product->release        = 'Release';
$lang->product->allRelease     = 'All Releases';
$lang->product->maintain       = 'Maintenance';
$lang->product->latestDynamic  = 'Verlauf';
$lang->product->plan           = 'Plan';
$lang->product->iteration      = 'Version Iteration';
$lang->product->iterationInfo  = '%s Iterations';
$lang->product->iterationView  = 'Details';
$lang->product->createdBy      = 'Created By';
$lang->product->createdDate    = 'Created Date';

$lang->product->searchStory  = 'Suche';
$lang->product->assignedToMe = 'Mir zuweisen';
$lang->product->openedByMe   = 'Von mir erstellt';
$lang->product->reviewedByMe = 'Von mir geprüft';
$lang->product->closedByMe   = 'Von mir geschlossen';
$lang->product->draftStory   = 'Entwurf';
$lang->product->activeStory  = 'Aktiviert';
$lang->product->changedStory = 'Geändert';
$lang->product->willClose    = 'Zu schließen';
$lang->product->closedStory  = 'Geschlossen';
$lang->product->unclosed     = 'Offen';
$lang->product->unplan       = 'Warten';
$lang->product->viewByUser   = 'By User';

$lang->product->allStory             = 'Alle';
$lang->product->allProduct           = 'Alle' . $lang->productCommon;
$lang->product->allProductsOfProject = 'Alle verknüpften' . $lang->productCommon;

$lang->product->typeList['']         = '';
$lang->product->typeList['normal']   = 'Normal';
$lang->product->typeList['branch']   = 'Multi Branch';
$lang->product->typeList['platform'] = 'Multi Platform';

$lang->product->typeTips = array();
$lang->product->typeTips['branch']   = '(für eigene Inhalte)';
$lang->product->typeTips['platform'] = '(für IOS, Android, PC, etc.)';

$lang->product->branchName['normal']   = '';
$lang->product->branchName['branch']   = 'Branch';
$lang->product->branchName['platform'] = 'Platform';

$lang->product->statusList['normal'] = 'Normal';
$lang->product->statusList['closed'] = 'Geschlossen';

$lang->product->aclList['private'] = "Privat {$lang->productCommon} ({$lang->executionCommon} Nur Teammitglieder)";
$lang->product->aclList['open']    = "Standard (Benutzer mit Rechten für {$lang->productCommon} können zugreifen.)";
//$lang->product->aclList['custom']  = 'Benutzerdefiniert (Teammitglieder und Whitelist Benutzer haben Zugriff.)';

$lang->product->acls['private'] = "Privat {$lang->productCommon}";
$lang->product->acls['open']    = "Standard";

$lang->product->aclTips['open']    = "Benutzer mit Rechten für {$lang->productCommon} können zugreifen.";
$lang->product->aclTips['private'] = "{$lang->executionCommon} Nur Teammitglieder";

$lang->product->storySummary   = " <strong>%s</strong> %s, <strong>%s</strong> Stunde(n) geplant, Fallabdeckung ist <strong>%s</strong> auf dieser Seite.";
$lang->product->checkedSummary = " <strong>%total%</strong> geprüft, <strong>%estimate%</strong> Stunde(n) geplant, Fallabdeckung ist <strong>%rate%</strong>.";
$lang->product->noModule       = '<div>Kein Modul</div><div>Jetzt verwalten</div>';
$lang->product->noProduct      = 'Kein Produkt. ';
$lang->product->noMatched      = '"%s" kann nicht gefunden werden.' . $lang->productCommon;

$lang->product->featureBar['browse']['allstory']     = $lang->product->allStory;
$lang->product->featureBar['browse']['unclosed']     = $lang->product->unclosed;
$lang->product->featureBar['browse']['assignedtome'] = $lang->product->assignedToMe;
$lang->product->featureBar['browse']['openedbyme']   = $lang->product->openedByMe;
$lang->product->featureBar['browse']['reviewedbyme'] = $lang->product->reviewedByMe;
$lang->product->featureBar['browse']['draftstory']   = $lang->product->draftStory;
$lang->product->featureBar['browse']['more']         = $lang->more;

$lang->product->featureBar['all']['all']      = $lang->product->allProduct;
$lang->product->featureBar['all']['noclosed'] = $lang->product->unclosed;
$lang->product->featureBar['all']['closed']   = $lang->product->statusList['closed'];

$lang->product->moreSelects['closedbyme']   = $lang->product->closedByMe;
$lang->product->moreSelects['activestory']  = $lang->product->activeStory;
$lang->product->moreSelects['changedstory'] = $lang->product->changedStory;
$lang->product->moreSelects['willclose']    = $lang->product->willClose;
$lang->product->moreSelects['closedstory']  = $lang->product->closedStory;
