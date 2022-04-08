<?php
/**
 * The product module English file of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     product
 * @version     $Id: en.php 5091 2013-07-10 06:06:46Z chencongzhi520@gmail.com $
 * @link        https://www.zentao.pm
 */
$lang->product->index         = 'Accueil ' . $lang->productCommon;
$lang->product->browse        = 'Liste Stories';
$lang->product->dynamic       = 'Historique';
$lang->product->view          = "{$lang->productCommon} Détail";
$lang->product->edit          = "Editer {$lang->productCommon}";
$lang->product->batchEdit     = 'Editer par Lot';
$lang->product->create        = "Créer {$lang->productCommon}";
$lang->product->delete        = "Supprimer {$lang->productCommon}";
$lang->product->deleted       = 'Supprimé';
$lang->product->close         = "Fermer";
$lang->product->closeAction   = "Fermer {$lang->productCommon}";
$lang->product->select        = "Choisir {$lang->productCommon}";
$lang->product->mine          = 'Les miens';
$lang->product->other         = 'Autres';
$lang->product->closed        = 'Fermés';
$lang->product->updateOrder   = 'Ordre';
$lang->product->orderAction   = "Rang {$lang->productCommon}";
$lang->product->all           = "{$lang->productCommon} List";
$lang->product->manageLine    = "Manage {$lang->productCommon} Line";
$lang->product->newLine       = "Create {$lang->productCommon} Line";
$lang->product->export        = 'Export';
$lang->product->exportAction  = "Export {$lang->productCommon}";
$lang->product->dashboard     = "Dashboard";
$lang->product->changeProgram = "{$lang->productCommon} confirmation of the scope of influence of adjustment of the program set";

$lang->product->basicInfo = 'Infos de Base';
$lang->product->otherInfo = 'Autres Infos';

$lang->product->plans       = 'Plans';
$lang->product->releases    = 'Releases';
$lang->product->docs        = 'Doc';
$lang->product->bugs        = 'Bug Liés';
$lang->product->projects    = "Linked Project";
$lang->product->executions  = "{$lang->execution->common}s Liés";
$lang->product->cases       = 'CasTests';
$lang->product->builds      = 'Build';
$lang->product->roadmap     = "Roadmap {$lang->productCommon}";
$lang->product->doc         = "Documents {$lang->productCommon}";
$lang->product->project     = ' Liste ' . $lang->executionCommon;
$lang->product->build       = 'Liste Builds';
$lang->product->moreProduct = "More Product";
$lang->product->projectInfo = "Les Projects qui sont associés à ce {$lang->productCommon} sont listés ci-dessous.";

$lang->product->currentExecution      = "Current Execution";
$lang->product->activeStories         = 'Actives [S]';
$lang->product->activeStoriesTitle    = 'Stories Actives';
$lang->product->changedStories        = 'Changées [S]';
$lang->product->changedStoriesTitle   = 'Stories Modifiées';
$lang->product->draftStories          = 'Brouillon [S]';
$lang->product->draftStoriesTitle     = 'Stories en Analyse';
$lang->product->closedStories         = 'Fermées [S]';
$lang->product->closedStoriesTitle    = 'Stories Fermées';
$lang->product->storyCompleteRate     = "{$lang->SRCommon} Completion rate";
$lang->product->activeRequirements    = "Active {$lang->URCommon}";
$lang->product->changedRequirements   = "Changed {$lang->URCommon}";
$lang->product->draftRequirements     = "Draft {$lang->URCommon}";
$lang->product->closedRequirements    = "Closed {$lang->URCommon}";
$lang->product->requireCompleteRate   = "{$lang->URCommon} Completion rate";
$lang->product->unResolvedBugs        = 'Ouverts [B]';
$lang->product->unResolvedBugsTitle   = 'Bugs Ouverts';
$lang->product->assignToNullBugs      = 'Orphelins [B]';
$lang->product->assignToNullBugsTitle = 'Bugs non assignés';
$lang->product->closedBugs            = 'Closed Bug';
$lang->product->bugFixedRate          = 'Bug Repair rate';

$lang->product->confirmDelete        = "Voulez-vous vraiment supprimer le {$lang->productCommon} ?";
$lang->product->errorNoProduct       = "Aucun {$lang->productCommon} n'est créé pour l'instant !";
$lang->product->accessDenied         = "Vous n'avez pas accès au {$lang->productCommon}.";
$lang->product->programChangeTip     = "The projects linked with this {$lang->productCommon}: %s will be transferred to the modified program set together.";
$lang->product->notChangeProgramTip  = "The {$lang->SRCommon} of {$lang->productCommon} has been linked to the following projects, please cancel the link before proceeding";
$lang->product->confirmChangeProgram = "The projects linked with this {$lang->productCommon}: %s is also linked with other products, whether to transfer projects to the modified program set.";
$lang->product->changeProgramError   = "The {$lang->SRCommon} of this {$lang->productCommon} has been linked to the project, please unlink it before proceeding";

$lang->product->id             = 'ID';
$lang->product->program        = "Program";
$lang->product->name           = "Nom du {$lang->productCommon}";
$lang->product->code           = 'Code';
$lang->product->line           = "{$lang->productCommon} Line";
$lang->product->lineName       = "{$lang->productCommon} Line Name";
$lang->product->order          = 'Rang';
$lang->product->type           = 'Type';
$lang->product->typeAB         = 'Type';
$lang->product->status         = 'Statut';
$lang->product->subStatus      = 'Sous-Statut';
$lang->product->desc           = 'Description';
$lang->product->manager        = 'Managers';
$lang->product->PO             = "{$lang->productCommon} Owner";
$lang->product->QD             = 'Quality Manager';
$lang->product->RD             = 'Release Manager';
$lang->product->acl            = "Contrôle accès";
$lang->product->whitelist      = 'Liste Blanche';
$lang->product->addWhitelist   = 'Add Whitelist';
$lang->product->unbindWhitelist = 'Remove Whitelist';
$lang->product->branch         = '%s';
$lang->product->qa             = 'QA';
$lang->product->release        = 'Release';
$lang->product->allRelease     = 'Toutes Releases';
$lang->product->maintain       = 'Maintenance';
$lang->product->latestDynamic  = 'Historique';
$lang->product->plan           = 'Plan';
$lang->product->iteration      = 'Itérations';
$lang->product->iterationInfo  = '%s Itération';
$lang->product->iterationView  = 'Détail';
$lang->product->createdBy      = 'Créé par';
$lang->product->createdDate    = 'Créé le';

$lang->product->searchStory  = 'Recherche';
$lang->product->assignedToMe = 'Affectées à Moi';
$lang->product->openedByMe   = 'Créées par Moi';
$lang->product->reviewedByMe = 'Validées par Moi';
$lang->product->closedByMe   = 'Fermées par Moi';
$lang->product->draftStory   = 'A étudier';
$lang->product->activeStory  = 'Actives';
$lang->product->changedStory = 'Changées';
$lang->product->willClose    = 'A Fermer';
$lang->product->closedStory  = 'Fermées';
$lang->product->unclosed     = 'Ouvertes';
$lang->product->unplan       = 'Non planifiées';
$lang->product->viewByUser   = 'Par Utilisateur';

$lang->product->allStory             = 'Toutes les Stories ';
$lang->product->allProduct           = 'Tous';
$lang->product->allProductsOfProject = 'Tous les ' . $lang->productCommon . ' Associés';

$lang->product->typeList['']         = '';
$lang->product->typeList['normal']   = 'Normal';
$lang->product->typeList['branch']   = 'Multi-Branche';
$lang->product->typeList['platform'] = 'Multi-Plateforme';

$lang->product->typeTips = array();
$lang->product->typeTips['branch']   = ' (pour des contextes personnalisés, ex : équipes offshore)';
$lang->product->typeTips['platform'] = ' (pour des applications multi-plateformes, ex : IOS, Android, PC, etc.)';

$lang->product->branchName['normal']   = '';
$lang->product->branchName['branch']   = 'Branche';
$lang->product->branchName['platform'] = 'Plateforme';

$lang->product->statusList['normal'] = 'Normal';
$lang->product->statusList['closed'] = 'Fermé';

$lang->product->aclList['private'] = "{$lang->productCommon} Privé (seuls les membres de l'équipe {$lang->executionCommon} ont les droits)";
$lang->product->aclList['open']    = "Défaut (Les utilisateurs ayant des droits sur {$lang->productCommon} peuvent accéder à ce {$lang->productCommon}.)";
//$lang->product->aclList['custom']  = "Personnalisé (les membres de l'équipe et les membres de la Liste blanche peuvent y accéder.)";

$lang->product->acls['private'] = "{$lang->productCommon} Privé";
$lang->product->acls['open']    = 'Défaut';

$lang->product->aclTips['open']    = "Les utilisateurs ayant des droits sur {$lang->productCommon} peuvent accéder à ce {$lang->productCommon}.";
$lang->product->aclTips['private'] = "les membres de l'équipe et les membres de la Liste blanche peuvent y accéder.";

$lang->product->storySummary   = "Total de <strong>%s</strong> %s sur cette page. Estimé: <strong>%s</strong> (h), et couverture de la recette: <strong>%s</strong>.";
$lang->product->checkedSummary = "<strong>%total%</strong> %storyCommon% sélectionnées, Estimé: <strong>%estimate%</strong>, et couverture de la recette: <strong>%rate%</strong>.";
$lang->product->noModule       = "<div>Vous n'avez aucun modules. </div><div>Gérer Maintenant</div>";
$lang->product->noProduct      = "No {$lang->productCommon} à ce jour. ";
$lang->product->noMatched      = '"%s" cannot be found.' . $lang->productCommon;

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
