<?php
/**
 * The action module fr file of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     action
 * @version     $Id: fr.php 4729 2013-05-03 07:53:55Z chencongzhi520@gmail.com $
 * @link        https://www.zentao.pm
 */
$lang->action->common     = 'Log';
$lang->action->product    = $lang->productCommon;
$lang->action->project    = 'Project';
$lang->action->execution  = $lang->execution->common;
$lang->action->objectType = "Type d'objet";
$lang->action->objectID   = 'ID';
$lang->action->objectName = "Nom de l'object";
$lang->action->actor      = 'User';
$lang->action->action     = 'Action';
$lang->action->actionID   = 'Action ID';
$lang->action->date       = 'Date';
$lang->action->extra      = 'Extra';

$lang->action->trash       = 'Corbeille';
$lang->action->undelete    = 'Restaurer';
$lang->action->hideOne     = 'Masquer';
$lang->action->hideAll     = 'Masquer Tout';
$lang->action->editComment = 'Editer commentaire';
$lang->action->create      = 'Ajouter commentaire';
$lang->action->comment     = 'Commentaire';

$lang->action->trashTips      = 'Note: Les suppressions dans ZenTao sont purement logiques.';
$lang->action->textDiff       = 'Text Format';
$lang->action->original       = 'Original Format';
$lang->action->confirmHideAll = 'Voulez-vous masquer tous les enregistrements ?';
$lang->action->needEdit       = '%s que vous voulez restaurer. SVP éditez la.';
$lang->action->historyEdit    = "l'historique Edité Par ne peut pas être vide.";
$lang->action->noDynamic      = "Pas d'historique.";

$lang->action->history = new stdclass();
$lang->action->history->action = 'Lien';
$lang->action->history->field  = 'Champ';
$lang->action->history->old    = 'Ancienne valeur';
$lang->action->history->new    = 'Nouvelle valeur';
$lang->action->history->diff   = 'Comparer';

$lang->action->dynamic = new stdclass();
$lang->action->dynamic->today      = "Aujourd'hui";
$lang->action->dynamic->yesterday  = 'Hier';
$lang->action->dynamic->twoDaysAgo = 'Il y a 2 jours';
$lang->action->dynamic->thisWeek   = 'Cette semaine';
$lang->action->dynamic->lastWeek   = 'La semaine dernière';
$lang->action->dynamic->thisMonth  = 'Ce mois';
$lang->action->dynamic->lastMonth  = 'Le mois dernier';
$lang->action->dynamic->all        = 'Tout';
$lang->action->dynamic->hidden     = 'Caché';
$lang->action->dynamic->search     = 'Rechercher';

$lang->action->periods['all']       = $lang->action->dynamic->all;
$lang->action->periods['today']     = $lang->action->dynamic->today;
$lang->action->periods['yesterday'] = $lang->action->dynamic->yesterday;
$lang->action->periods['thisweek']  = $lang->action->dynamic->thisWeek;
$lang->action->periods['lastweek']  = $lang->action->dynamic->lastWeek;
$lang->action->periods['thismonth'] = $lang->action->dynamic->thisMonth;
$lang->action->periods['lastmonth'] = $lang->action->dynamic->lastMonth;

$lang->action->objectTypes['product']     = $lang->productCommon;
$lang->action->objectTypes['branch']      = 'Branche';
$lang->action->objectTypes['story']       = $lang->SRCommon;
$lang->action->objectTypes['design']      = 'Design';
$lang->action->objectTypes['productplan'] = 'Plan';
$lang->action->objectTypes['release']     = 'Release';
$lang->action->objectTypes['program']     = 'Program';
$lang->action->objectTypes['project']     = 'Project';
$lang->action->objectTypes['execution']   = $lang->executionCommon;
$lang->action->objectTypes['task']        = 'Tâche';
$lang->action->objectTypes['build']       = 'Build';
$lang->action->objectTypes['job']         = 'Job';
$lang->action->objectTypes['bug']         = 'Bug';
$lang->action->objectTypes['case']        = 'CasTest';
$lang->action->objectTypes['caseresult']  = 'CasTest Result';
$lang->action->objectTypes['stepresult']  = 'CasTest Steps';
$lang->action->objectTypes['caselib']     = 'Library';
$lang->action->objectTypes['testsuite']   = 'Cahier recette';
$lang->action->objectTypes['testtask']    = 'Recette';
$lang->action->objectTypes['testreport']  = 'Edition';
$lang->action->objectTypes['doc']         = 'Document';
$lang->action->objectTypes['doclib']      = 'Répertoire Documents';
$lang->action->objectTypes['todo']        = 'Todo';
$lang->action->objectTypes['risk']        = 'Risk';
$lang->action->objectTypes['issue']       = 'Issue';
$lang->action->objectTypes['module']      = 'Module';
$lang->action->objectTypes['user']        = 'Utilisateur';
$lang->action->objectTypes['stakeholder'] = 'Stakeholder';
$lang->action->objectTypes['budget']      = 'Cost Estimate';
$lang->action->objectTypes['entry']       = 'Entrée';
$lang->action->objectTypes['webhook']     = 'Webhook';
$lang->action->objectTypes['job']         = 'Job';
$lang->action->objectTypes['team']        = 'Team';
$lang->action->objectTypes['whitelist']   = 'Whitelist';
$lang->action->objectTypes['pipeline']    = 'GitLib';

/* 用来描述操作历史记录。*/
$lang->action->desc = new stdclass();
$lang->action->desc->common          = '$date, <strong>$action</strong> par <strong>$actor</strong>.' . "\n";
$lang->action->desc->extra           = '$date, <strong>$action</strong> en tant que <strong>$extra</strong> par <strong>$actor</strong>.' . "\n";
$lang->action->desc->opened          = '$date, initié par <strong>$actor</strong> .' . "\n";
$lang->action->desc->openedbysystem  = '$date, opened by system.' . "\n";
$lang->action->desc->created         = '$date, créé par <strong>$actor</strong> .' . "\n";
$lang->action->desc->added           = '$date, ajouter par  <strong>$actor</strong> .' . "\n";
$lang->action->desc->changed         = '$date, modifié par <strong>$actor</strong> .' . "\n";
$lang->action->desc->edited          = '$date, édité par <strong>$actor</strong> .' . "\n";
$lang->action->desc->assigned        = '$date, <strong>$actor</strong> affecté à <strong>$extra</strong>.' . "\n";
$lang->action->desc->closed          = '$date, fermé par <strong>$actor</strong> .' . "\n";
$lang->action->desc->closedbysystem  = '$date, closed by system.' . "\n";
$lang->action->desc->deleted         = '$date, supprimé par <strong>$actor</strong> .' . "\n";
$lang->action->desc->deletedfile     = '$date, <strong>$actor</strong> a supprimé <strong><i>$extra</i></strong>.' . "\n";
$lang->action->desc->editfile        = '$date, <strong>$actor</strong> a édité <strong><i>$extra</i></strong>.' . "\n";
$lang->action->desc->erased          = '$date, supprimé par <strong>$actor</strong> .' . "\n";
$lang->action->desc->undeleted       = '$date, restauré par <strong>$actor</strong> .' . "\n";
$lang->action->desc->hidden          = '$date, masqué par <strong>$actor</strong> .' . "\n";
$lang->action->desc->commented       = '$date, ajouté par <strong>$actor</strong>.' . "\n";
$lang->action->desc->activated       = '$date, activé par <strong>$actor</strong> .' . "\n";
$lang->action->desc->blocked         = '$date, bloqué par <strong>$actor</strong> .' . "\n";
$lang->action->desc->moved           = '$date, déplacé par <strong>$actor</strong> , qui était "$extra".' . "\n";
$lang->action->desc->confirmed       = '$date, <strong>$actor</strong> a confirmé la modification de la story. Le dernier build est <strong>#$extra</strong>.' . "\n";
$lang->action->desc->caseconfirmed   = '$date, <strong>$actor</strong> a confirmé la modification du CasTest. Le dernier build est <strong>#$extra</strong>' . "\n";
$lang->action->desc->bugconfirmed    = '$date, <strong>$actor</strong> a confirmé le Bug.' . "\n";
$lang->action->desc->frombug         = '$date, converti par <strong>$actor</strong>. Son ID était <strong>$extra</strong>.';
$lang->action->desc->started         = '$date, commencé par <strong>$actor</strong>.' . "\n";
$lang->action->desc->restarted       = '$date, reprise par <strong>$actor</strong>.' . "\n";
$lang->action->desc->delayed         = '$date, ajourné par <strong>$actor</strong>.' . "\n";
$lang->action->desc->suspended       = '$date, suspendu par <strong>$actor</strong>.' . "\n";
$lang->action->desc->recordestimate  = '$date, enregistré par <strong>$actor</strong> et son coût est de <strong>$extra</strong> heures.';
$lang->action->desc->editestimate    = '$date, <strong>$actor</strong> a édité son temps estimé.';
$lang->action->desc->deleteestimate  = '$date, <strong>$actor</strong> a supprimé son estimation de temps.';
$lang->action->desc->canceled        = '$date, annulé par <strong>$actor</strong>.' . "\n";
$lang->action->desc->svncommited     = '$date, <strong>$actor</strong> a validé, le build est <strong>#$extra</strong>.' . "\n";
$lang->action->desc->gitcommited     = '$date, <strong>$actor</strong> a validé, le build est <strong>#$extra</strong>.' . "\n";
$lang->action->desc->finished        = '$date, terminé par <strong>$actor</strong>.' . "\n";
$lang->action->desc->paused          = '$date, mis en pause par <strong>$actor</strong>.' . "\n";
$lang->action->desc->verified        = '$date, verifié par <strong>$actor</strong>.' . "\n";
$lang->action->desc->diff1           = '<strong><i>%s</i></strong> a changé. Il est passé de "%s" à "%s".<br />' . "\n";
$lang->action->desc->diff2           = '<strong><i>%s</i></strong> a changé. La différence est ' . "\n" . "<blockquote class='textdiff'>%s</blockquote>" . "\n<blockquote class='original'>%s</blockquote>";
$lang->action->desc->diff3           = 'Le nom du fichier %s a changé pour %s .' . "\n";
$lang->action->desc->linked2bug      = '$date, affecté à <strong>$extra</strong> par <strong>$actor</strong>';
$lang->action->desc->linked2testtask = '$date, linked to <strong>$extra</strong> by <strong>$actor</strong>';
$lang->action->desc->resolved        = '$date, resolved by <strong>$actor</strong> ' . "\n";
$lang->action->desc->managed         = '$date, by <strong>$actor</strong> managed.' . "\n";
$lang->action->desc->estimated       = '$date, by <strong>$actor</strong> estimated.' . "\n";
$lang->action->desc->run             = '$date, by <strong>$actor</strong> executed.' . "\n";
$lang->action->desc->syncprogram     = '$date, started by <strong>$actor</strong>(starting the project sets the program status as Ongoing).' . "\n";
$lang->action->desc->syncproject     = '$date, starting the execution sets the project status as Ongoing.' . "\n";
$lang->action->desc->syncexecution   = '$date, starting the task sets the execution status as Ongoing.' . "\n";

/* 子任务修改父任务的历史操作记录 */
$lang->action->desc->createchildren     = '$date, <strong>$actor</strong> a créé un sous-tâche <strong>$extra</strong>。' . "\n";
$lang->action->desc->linkchildtask      = '$date, <strong>$actor</strong> a raccroché une sous-tâche <strong>$extra</strong>。' . "\n";
$lang->action->desc->unlinkchildrentask = '$date, <strong>$actor</strong> a décroché une sous-tâche <strong>$extra</strong>。' . "\n";
$lang->action->desc->linkparenttask     = '$date, <strong>$actor</strong> a accroché une tâche parent <strong>$extra</strong>。' . "\n";
$lang->action->desc->unlinkparenttask   = '$date, <strong>$actor</strong> a décroché une tâche parent <strong>$extra</strong>。' . "\n";
$lang->action->desc->deletechildrentask = '$date, <strong>$actor</strong> a supprimé une sous-tâche <strong>$extra</strong>。' . "\n";

/* 用来描述和父子需求相关的操作历史记录。*/
$lang->action->desc->createchildrenstory = '$date, <strong>$actor</strong> a créé une sous-tory <strong>$extra</strong>。' . "\n";
$lang->action->desc->linkchildstory      = '$date, <strong>$actor</strong> a raccroché une sous-story <strong>$extra</strong>。' . "\n";
$lang->action->desc->unlinkchildrenstory = '$date, <strong>$actor</strong> a décroché une sous-story <strong>$extra</strong>。' . "\n";
$lang->action->desc->linkparentstory     = '$date, <strong>$actor</strong> a raccroché à une story parent <strong>$extra</strong>。' . "\n";
$lang->action->desc->unlinkparentstory   = '$date, <strong>$actor</strong> a décroché de la story parent <strong>$extra</strong>。' . "\n";
$lang->action->desc->deletechildrenstory = '$date, <strong>$actor</strong> a supprimé une sous-story <strong>$extra</strong>。' . "\n";

/* 关联用例和移除用例时的历史操作记录。*/
$lang->action->desc->linkrelatedcase   = '$date, <strong>$actor</strong> a fait le lien avec un CasTest <strong>$extra</strong>.' . "\n";
$lang->action->desc->unlinkrelatedcase = '$date, <strong>$actor</strong> a délié le CasTest <strong>$extra</strong>.' . "\n";

/* 用来显示动态信息。*/
$lang->action->label                        = new stdclass();
$lang->action->label->created               = 'a créé ';
$lang->action->label->opened                = 'a ouvert ';
$lang->action->label->openedbysystem        = 'Opened by system ';
$lang->action->label->closedbysystem        = 'Closed by system ';
$lang->action->label->added                 = 'ajouter';
$lang->action->label->changed               = 'a changé ';
$lang->action->label->edited                = 'a édité ';
$lang->action->label->assigned              = 'a affecté ';
$lang->action->label->closed                = 'a fermé ';
$lang->action->label->deleted               = 'a supprimé ';
$lang->action->label->deletedfile           = 'a supprimé ';
$lang->action->label->editfile              = 'a édité ';
$lang->action->label->erased                = 'a détruit ';
$lang->action->label->undeleted             = 'a restauré ';
$lang->action->label->hidden                = 'a masqué ';
$lang->action->label->commented             = 'a commenté ';
$lang->action->label->communicated          = 'communicated';
$lang->action->label->activated             = 'a activé ';
$lang->action->label->blocked               = 'a bloqué ';
$lang->action->label->resolved              = 'a résolu ';
$lang->action->label->reviewed              = 'a revu ';
$lang->action->label->recalled              = 'recalled';
$lang->action->label->moved                 = 'a déplacé ';
$lang->action->label->confirmed             = 'a confirmé Story ';
$lang->action->label->bugconfirmed          = 'a confirmé le Bug ';
$lang->action->label->tostory               = 'a converti en Story ';
$lang->action->label->frombug               = 'a converti du Bug ';
$lang->action->label->fromlib               = 'a importé de la Library ';
$lang->action->label->totask                = 'a converti en Tâche ';
$lang->action->label->svncommited           = 'a committé SVN ';
$lang->action->label->gitcommited           = 'a committé GIT ';
$lang->action->label->linked2plan           = 'a raccordé au Plan ';
$lang->action->label->unlinkedfromplan      = 'a extrait du plan ';
$lang->action->label->changestatus          = 'a changé le statut ';
$lang->action->label->marked                = 'a marqué';
$lang->action->label->linked2execution      = "a ajouté à {$lang->executionCommon}";
$lang->action->label->unlinkedfromexecution = "a enlevé de {$lang->executionCommon}";
$lang->action->label->linked2project        = "Link Project";
$lang->action->label->unlinkedfromproject   = "Unlink Project";
$lang->action->label->unlinkedfrombuild     = "a enlevé du Build ";
$lang->action->label->linked2release        = "a ajouté à une Release ";
$lang->action->label->unlinkedfromrelease   = "a enlevé de la Release ";
$lang->action->label->linkrelatedbug        = "a lié à un Bug ";
$lang->action->label->unlinkrelatedbug      = "a délié du Bug ";
$lang->action->label->linkrelatedcase       = "a mis en relation avec un CasTest ";
$lang->action->label->unlinkrelatedcase     = "a libéré du CasTest ";
$lang->action->label->linkrelatedstory      = "a lié à une Story ";
$lang->action->label->unlinkrelatedstory    = "a dégagé de la Story ";
$lang->action->label->subdividestory        = "a décomposé la Story ";
$lang->action->label->unlinkchildstory      = "a dissocié de la Story fille ";
$lang->action->label->started               = 'a commencé ';
$lang->action->label->restarted             = 'a continué ';
$lang->action->label->recordestimate        = 'a estimé ';
$lang->action->label->editestimate          = 'a réestimé ';
$lang->action->label->canceled              = 'a annulé ';
$lang->action->label->finished              = 'a fini ';
$lang->action->label->paused                = 'a stoppé ';
$lang->action->label->verified              = 'a vérifié ';
$lang->action->label->delayed               = 'a ajourné ';
$lang->action->label->suspended             = 'a suspendu ';
$lang->action->label->login                 = "s'est Connecté";
$lang->action->label->logout                = "s'est Déconnecté";
$lang->action->label->notified              = "Notified";
$lang->action->label->deleteestimate        = "a remis à zéro ";
$lang->action->label->linked2build          = "a ajouté au Build ";
$lang->action->label->linked2bug            = "a lié au Bug ";
$lang->action->label->linked2testtask       = "linked";
$lang->action->label->unlinkedfromtesttask  = "unlinked";
$lang->action->label->linkchildtask         = "a raccroché à une sous-tâche";
$lang->action->label->unlinkchildrentask    = "a décroché la sous-tâche";
$lang->action->label->linkparenttask        = "a raccroché à une tâche parent";
$lang->action->label->unlinkparenttask      = "a décroché de la tâche parent";
$lang->action->label->batchcreate           = "a créé tâches par lot";
$lang->action->label->createchildren        = "a créé sous-tâches par lot";
$lang->action->label->managed               = "a managé";
$lang->action->label->managedteam           = "managed";
$lang->action->label->managedwhitelist      = "managed";
$lang->action->label->deletechildrentask    = "a supprimé une sous-tâche";
$lang->action->label->createchildrenstory   = "a créé une sous-story";
$lang->action->label->linkchildstory        = "a raccroché à une sous-story";
$lang->action->label->unlinkchildrenstory   = "a décroché de la sous-story";
$lang->action->label->linkparentstory       = "a raccroché à une story parent";
$lang->action->label->unlinkparentstory     = "a décroché de la story parent";
$lang->action->label->deletechildrenstory   = "a supprimé la sous-story";
$lang->action->label->tracked               = 'tracked';
$lang->action->label->hangup                = 'hangup';
$lang->action->label->run                   = 'run';
$lang->action->label->estimated             = 'estimated';
$lang->action->label->reviewpassed          = 'Pass';
$lang->action->label->reviewrejected        = 'Reject';
$lang->action->label->reviewclarified       = 'Clarify';
$lang->action->label->commitsummary         = 'Commit Summary';
$lang->action->label->updatetrainee         = 'Update Trainee';
$lang->action->label->syncprogram           = 'start';
$lang->action->label->syncproject           = 'start';
$lang->action->label->syncexecution         = 'start';
$lang->action->label->startProgram          = '(The start of the project sets the status of the program as Ongoing)';

/* 动态信息按照对象分组 */
$lang->action->dynamicAction                    = new stdclass();
$lang->action->dynamicAction->todo['opened']    = 'Créer Todo';
$lang->action->dynamicAction->todo['edited']    = 'Edit Todo';
$lang->action->dynamicAction->todo['erased']    = 'Détruire Todo';
$lang->action->dynamicAction->todo['finished']  = 'Finir Todo';
$lang->action->dynamicAction->todo['activated'] = 'Activer Todo';
$lang->action->dynamicAction->todo['closed']    = 'Fermer Todo';
$lang->action->dynamicAction->todo['assigned']  = 'Assigner Todo';
$lang->action->dynamicAction->todo['undeleted'] = 'Restaurer Todo';
$lang->action->dynamicAction->todo['hidden']    = 'Masquer Todo';

$lang->action->dynamicAction->program['create']   = 'Create Program';
$lang->action->dynamicAction->program['edit']     = 'Edit Program';
$lang->action->dynamicAction->program['activate'] = 'Activate Program';
$lang->action->dynamicAction->program['delete']   = 'Delete Program';
$lang->action->dynamicAction->program['close']    = 'Close Program';

$lang->action->dynamicAction->project['create']   = 'Create Project';
$lang->action->dynamicAction->project['edit']     = 'Edit Project';
$lang->action->dynamicAction->project['start']    = 'Start Project';
$lang->action->dynamicAction->project['suspend']  = 'Suspend Project';
$lang->action->dynamicAction->project['activate'] = 'Activate Project';
$lang->action->dynamicAction->project['close']    = 'Close Project';

$lang->action->dynamicAction->product['opened']    = 'Créer ' . $lang->productCommon;
$lang->action->dynamicAction->product['edited']    = 'Editer ' . $lang->productCommon;
$lang->action->dynamicAction->product['deleted']   = 'Supprimer ' . $lang->productCommon;
$lang->action->dynamicAction->product['closed']    = 'Fermer ' . $lang->productCommon;
$lang->action->dynamicAction->product['undeleted'] = 'Restaurer ' . $lang->productCommon;
$lang->action->dynamicAction->product['hidden']    = 'Masquer ' . $lang->productCommon;

$lang->action->dynamicAction->productplan['opened'] = 'Créer Plan';
$lang->action->dynamicAction->productplan['edited'] = 'Editer Plan';

$lang->action->dynamicAction->release['opened']       = 'Créer Release';
$lang->action->dynamicAction->release['edited']       = 'Editer Release';
$lang->action->dynamicAction->release['changestatus'] = 'Changer Statut Release';
$lang->action->dynamicAction->release['undeleted']    = 'Restaurer Release';
$lang->action->dynamicAction->release['notified']     = 'Notify Release';
$lang->action->dynamicAction->release['hidden']       = 'Masquer Release';

$lang->action->dynamicAction->story['opened']                = 'Créer Story';
$lang->action->dynamicAction->story['edited']                = 'Editer Story';
$lang->action->dynamicAction->story['activated']             = 'Activer Story';
$lang->action->dynamicAction->story['reviewed']              = 'Consulter Story';
$lang->action->dynamicAction->story['recalled']              = 'Revoke';
$lang->action->dynamicAction->story['closed']                = 'Fermer Story';
$lang->action->dynamicAction->story['assigned']              = 'Assigner Story';
$lang->action->dynamicAction->story['changed']               = 'Changer Story';
$lang->action->dynamicAction->story['linked2plan']           = 'Inclure Story au Plan';
$lang->action->dynamicAction->story['unlinkedfromplan']      = 'Enlever Story du Plan';
$lang->action->dynamicAction->story['linked2release']        = 'Inclure Story à la Release';
$lang->action->dynamicAction->story['unlinkedfromrelease']   = 'Enlever Story de la Release';
$lang->action->dynamicAction->story['linked2build']          = 'Ajouter Story au Build';
$lang->action->dynamicAction->story['unlinkedfrombuild']     = 'Détacher Story du Build';
$lang->action->dynamicAction->story['unlinkedfromproject']   = 'Détacher du Project';
$lang->action->dynamicAction->story['undeleted']             = 'Restaurer Story';
$lang->action->dynamicAction->story['hidden']                = 'Masquer Story';
$lang->action->dynamicAction->story['linked2execution']      = "Link Story";
$lang->action->dynamicAction->story['unlinkedfromexecution'] = "Unlink Story";
$lang->action->dynamicAction->story['estimated']             = "Estimate $lang->SRCommon";

$lang->action->dynamicAction->project['opened']    = 'Créer ' . $lang->executionCommon;
$lang->action->dynamicAction->project['edited']    = 'Editer ' . $lang->executionCommon;
$lang->action->dynamicAction->project['deleted']   = 'Supprimer ' . $lang->executionCommon;
$lang->action->dynamicAction->project['started']   = 'Commencer ' . $lang->executionCommon;
$lang->action->dynamicAction->project['delayed']   = 'Ajouter ' . $lang->executionCommon;
$lang->action->dynamicAction->project['suspended'] = 'Suspendre ' . $lang->executionCommon;
$lang->action->dynamicAction->project['activated'] = 'Activer ' . $lang->executionCommon;
$lang->action->dynamicAction->project['closed']    = 'Fermer ' . $lang->executionCommon;
$lang->action->dynamicAction->project['managed']   = 'Manager ' . $lang->executionCommon;
$lang->action->dynamicAction->project['undeleted'] = 'Restaurer ' . $lang->executionCommon;
$lang->action->dynamicAction->project['hidden']    = 'Masquer ' . $lang->executionCommon;
$lang->action->dynamicAction->project['moved']     = 'Déplacer Tâche';

$lang->action->dynamicAction->execution['opened']    = 'Create ' . $lang->executionCommon;
$lang->action->dynamicAction->execution['edited']    = 'Edit ' . $lang->executionCommon;
$lang->action->dynamicAction->execution['deleted']   = 'Delete ' . $lang->executionCommon;
$lang->action->dynamicAction->execution['started']   = 'Start ' . $lang->executionCommon;
$lang->action->dynamicAction->execution['delayed']   = 'Delay ' . $lang->executionCommon;
$lang->action->dynamicAction->execution['suspended'] = 'Suspend ' . $lang->executionCommon;
$lang->action->dynamicAction->execution['activated'] = 'Activate ' . $lang->executionCommon;
$lang->action->dynamicAction->execution['closed']    = 'Close ' . $lang->executionCommon;
$lang->action->dynamicAction->execution['managed']   = 'Manage ' . $lang->executionCommon;
$lang->action->dynamicAction->execution['undeleted'] = 'Restore ' . $lang->executionCommon;
$lang->action->dynamicAction->execution['hidden']    = 'Hide ' . $lang->executionCommon;
$lang->action->dynamicAction->execution['moved']     = 'Improt Task';

$lang->action->dynamicAction->team['managedTeam'] = 'Manage Team';

$lang->action->dynamicAction->task['opened']              = 'Créer Tâche';
$lang->action->dynamicAction->task['edited']              = 'Editer Tâche';
$lang->action->dynamicAction->task['commented']           = 'Commenter Tâche';
$lang->action->dynamicAction->task['assigned']            = 'Assigner Tâche';
$lang->action->dynamicAction->task['confirmed']           = 'Confirmer Tâche';
$lang->action->dynamicAction->task['started']             = 'Commencer Tâche';
$lang->action->dynamicAction->task['finished']            = 'Terminer Tâche';
$lang->action->dynamicAction->task['recordestimate']      = 'Ajouter Estimation';
$lang->action->dynamicAction->task['editestimate']        = 'Editer Estimation';
$lang->action->dynamicAction->task['deleteestimate']      = 'RAZ Estimation';
$lang->action->dynamicAction->task['paused']              = 'Stopper Tâche';
$lang->action->dynamicAction->task['closed']              = 'Fermer Tâche';
$lang->action->dynamicAction->task['canceled']            = 'Supprimer Tâche';
$lang->action->dynamicAction->task['activated']           = 'Activer Tâche';
$lang->action->dynamicAction->task['createchildren']      = 'Créer Sous-Tâche';
$lang->action->dynamicAction->task['unlinkparenttask']    = 'Délier Tâche Parent';
$lang->action->dynamicAction->task['deletechildrentask']  = 'Delete children task';
$lang->action->dynamicAction->task['linkparenttask']      = 'Lier Tâche Parent';
$lang->action->dynamicAction->task['linkchildtask']       = 'Lier sous-tâche';
$lang->action->dynamicAction->task['createchildrenstory'] = 'Créer sous-story';
$lang->action->dynamicAction->task['unlinkparentstory']   = 'Délier Story Parent';
$lang->action->dynamicAction->task['deletechildrenstory'] = 'Supprimer sous-story';
$lang->action->dynamicAction->task['linkparentstory']     = 'Lier Story Parent';
$lang->action->dynamicAction->task['linkchildstory']      = 'Lier sous-Story';
$lang->action->dynamicAction->task['undeleted']           = 'Restaurer Tâche';
$lang->action->dynamicAction->task['hidden']              = 'Masquer Tâche';
$lang->action->dynamicAction->task['svncommited']         = 'Committer SVN';
$lang->action->dynamicAction->task['gitcommited']         = 'Committer GIT';

$lang->action->dynamicAction->build['opened']  = 'Créer Build';
$lang->action->dynamicAction->build['edited']  = 'Editer Build';
$lang->action->dynamicAction->build['deleted'] = 'Delete Build';

$lang->action->dynamicAction->bug['opened']              = 'Remonter Bug';
$lang->action->dynamicAction->bug['edited']              = 'Editer Bug';
$lang->action->dynamicAction->bug['activated']           = 'Activer Bug';
$lang->action->dynamicAction->bug['assigned']            = 'Assigner Bug';
$lang->action->dynamicAction->bug['closed']              = 'Fermer Bug';
$lang->action->dynamicAction->bug['bugconfirmed']        = 'Confirmer Bug';
$lang->action->dynamicAction->bug['resolved']            = 'Resolu Bug';
$lang->action->dynamicAction->bug['undeleted']           = 'Restaurer Bug';
$lang->action->dynamicAction->bug['hidden']              = 'Masquer Bug';
$lang->action->dynamicAction->bug['deleted']             = 'Supprimé Bug';
$lang->action->dynamicAction->bug['confirmed']           = 'Confirme Chang.Story';
$lang->action->dynamicAction->bug['tostory']             = 'Converti en Story';
$lang->action->dynamicAction->bug['totask']              = 'Converti en Tâche';
$lang->action->dynamicAction->bug['linked2plan']         = 'Lié au Plan';
$lang->action->dynamicAction->bug['unlinkedfromplan']    = 'Enlevé du Plan';
$lang->action->dynamicAction->bug['linked2release']      = 'Ajouté à la Release';
$lang->action->dynamicAction->bug['unlinkedfromrelease'] = 'Enlevé de la Release';
$lang->action->dynamicAction->bug['linked2bug']          = 'Lié au Build';
$lang->action->dynamicAction->bug['unlinkedfrombuild']   = 'Retiré du Build';

$lang->action->dynamicAction->testtask['opened']    = 'Créé Recette';
$lang->action->dynamicAction->testtask['edited']    = 'Edité Recette';
$lang->action->dynamicAction->testtask['started']   = 'Commencé Recette';
$lang->action->dynamicAction->testtask['activated'] = 'Activé Recette';
$lang->action->dynamicAction->testtask['closed']    = 'Fermé Recette';
$lang->action->dynamicAction->testtask['blocked']   = 'Bloqué Recette';

$lang->action->dynamicAction->case['opened']    = 'Créé CasTest';
$lang->action->dynamicAction->case['edited']    = 'Edité CasTest';
$lang->action->dynamicAction->case['deleted']   = 'Supprimé CasTest';
$lang->action->dynamicAction->case['undeleted'] = 'Restauré CasTest';
$lang->action->dynamicAction->case['hidden']    = 'Masqué CasTest';
$lang->action->dynamicAction->case['reviewed']  = 'Ajouté Revue Résultats';
$lang->action->dynamicAction->case['confirmed'] = 'Confirmé CasTest';
$lang->action->dynamicAction->case['fromlib']   = 'Importé de CasTest Lib';

$lang->action->dynamicAction->testreport['opened']    = 'Créé Test Report';
$lang->action->dynamicAction->testreport['edited']    = 'Edité Test Report';
$lang->action->dynamicAction->testreport['deleted']   = 'Supprimé Test Report';
$lang->action->dynamicAction->testreport['undeleted'] = 'Restauré Test Report';
$lang->action->dynamicAction->testreport['hidden']    = 'Masqué Test Report';

$lang->action->dynamicAction->testsuite['opened']    = 'Créé Test Suite';
$lang->action->dynamicAction->testsuite['edited']    = 'Edité Test Suite';
$lang->action->dynamicAction->testsuite['deleted']   = 'Supprimé Test Suite';
$lang->action->dynamicAction->testsuite['undeleted'] = 'Restauré Test Suite';
$lang->action->dynamicAction->testsuite['hidden']    = 'Masqué Test Suite';

$lang->action->dynamicAction->caselib['opened']    = 'Créé CasTest Lib';
$lang->action->dynamicAction->caselib['edited']    = 'Edité CasTest Lib';
$lang->action->dynamicAction->caselib['deleted']   = 'Supprimé CasTest Lib';
$lang->action->dynamicAction->caselib['undeleted'] = 'Restauré CasTest Lib';
$lang->action->dynamicAction->caselib['hidden']    = 'Masqué CasTest Lib';

$lang->action->dynamicAction->doclib['created'] = 'Créer Doc Library';
$lang->action->dynamicAction->doclib['edited']  = 'Editer Doc Library';
$lang->action->dynamicAction->doclib['deleted'] = 'Delete Doc Library';

$lang->action->dynamicAction->doc['created']   = 'Créer Document';
$lang->action->dynamicAction->doc['edited']    = 'Editer Document';
$lang->action->dynamicAction->doc['commented'] = 'Commenter Document';
$lang->action->dynamicAction->doc['deleted']   = 'Supprimer Document';
$lang->action->dynamicAction->doc['undeleted'] = 'Restaurer Document';
$lang->action->dynamicAction->doc['hidden']    = 'Masquer Document';

$lang->action->dynamicAction->user['created']       = 'Créer User';
$lang->action->dynamicAction->user['edited']        = 'Editer User';
$lang->action->dynamicAction->user['deleted']       = 'Delete User';
$lang->action->dynamicAction->user['login']         = 'Connexion';
$lang->action->dynamicAction->user['logout']        = 'Déconnexion';
$lang->action->dynamicAction->user['undeleted']     = 'Restaure User';
$lang->action->dynamicAction->user['hidden']        = 'Masquer User';
$lang->action->dynamicAction->user['loginxuanxuan'] = 'Connexion Desktop';

$lang->action->dynamicAction->entry['created'] = 'Ajouter Application';
$lang->action->dynamicAction->entry['edited']  = 'Editer Application';

/* 用来生成相应对象的链接。*/
$lang->action->label->product     = $lang->productCommon . '|product|view|productID=%s';
$lang->action->label->productplan = 'Plan|productplan|view|productID=%s';
$lang->action->label->release     = 'Release|release|view|productID=%s';
$lang->action->label->story       = 'Story|story|view|storyID=%s';
$lang->action->label->program     = "Program|program|pgmproduct|programID=%s";
$lang->action->label->project     = "Project|program|index|projectID=%s";
if($config->systemMode == 'new')
{
    $lang->action->label->execution = "Execution|execution|task|executionID=%s";
}
else
{
    $lang->action->label->execution = "$lang->executionCommon|execution|task|executionID=%s";
}
$lang->action->label->task        = 'Tâche|task|view|taskID=%s';
$lang->action->label->build       = 'Build|build|view|buildID=%s';
$lang->action->label->bug         = 'Bug|bug|view|bugID=%s';
$lang->action->label->case        = 'CasTest|testcase|view|caseID=%s';
$lang->action->label->testtask    = 'Recette|testtask|view|caseID=%s';
$lang->action->label->testsuite   = 'Cahier Recette|testsuite|view|suiteID=%s';
$lang->action->label->caselib     = 'Library Recette|testsuite|libview|libID=%s';
$lang->action->label->todo        = 'Agenda|todo|view|todoID=%s';
$lang->action->label->doclib      = 'Bibliothèque|doc|browse|libID=%s';
$lang->action->label->doc         = 'Document|doc|view|docID=%s';
$lang->action->label->user        = 'Utilisateur|user|view|account=%s';
$lang->action->label->testreport  = 'Rapport|testreport|view|report=%s';
$lang->action->label->entry       = 'Application|entry|browse|';
$lang->action->label->webhook     = 'Webhook|webhook|browse|';
$lang->action->label->space       = ' ';
$lang->action->label->risk        = 'Risk|risk|view|riskID=%s';
$lang->action->label->issue       = 'Issue|issue|view|issueID=%s';
$lang->action->label->design      = 'Design|design|view|designID=%s';
$lang->action->label->stakeholder = 'Stakeholder|stakeholder|view|userID=%s';

/* Object type. */
$lang->action->search = new stdclass();
$lang->action->search->objectTypeList['']            = '';
$lang->action->search->objectTypeList['product']     = $lang->productCommon;
$lang->action->search->objectTypeList['program']     = 'Program';
$lang->action->search->objectTypeList['project']     = 'Project';
$lang->action->search->objectTypeList['execution']   = 'Execution';
$lang->action->search->objectTypeList['bug']         = 'Bug';
$lang->action->search->objectTypeList['case']        = 'CasTest';
$lang->action->search->objectTypeList['caseresult']  = 'CasTest Result';
$lang->action->search->objectTypeList['stepresult']  = 'CasTest Steps';
$lang->action->search->objectTypeList['story']       = "$lang->SRCommon/$lang->URCommon";
$lang->action->search->objectTypeList['task']        = 'Tâche';
$lang->action->search->objectTypeList['testtask']    = 'Recette';
$lang->action->search->objectTypeList['user']        = 'Utilisateur';
$lang->action->search->objectTypeList['doc']         = 'Doc';
$lang->action->search->objectTypeList['doclib']      = 'Doc Lib';
$lang->action->search->objectTypeList['todo']        = 'Todo';
$lang->action->search->objectTypeList['build']       = 'Build';
$lang->action->search->objectTypeList['release']     = 'Release';
$lang->action->search->objectTypeList['productplan'] = 'Plan';
$lang->action->search->objectTypeList['branch']      = 'Branche';
$lang->action->search->objectTypeList['testsuite']   = 'Cahier Recette';
$lang->action->search->objectTypeList['caselib']     = 'Library';
$lang->action->search->objectTypeList['testreport']  = 'Etat';

/* 用来在动态显示中显示动作 */
$lang->action->search->label['']                      = '';
$lang->action->search->label['created']               = $lang->action->label->created;
$lang->action->search->label['opened']                = $lang->action->label->opened;
$lang->action->search->label['changed']               = $lang->action->label->changed;
$lang->action->search->label['edited']                = $lang->action->label->edited;
$lang->action->search->label['assigned']              = $lang->action->label->assigned;
$lang->action->search->label['closed']                = $lang->action->label->closed;
$lang->action->search->label['deleted']               = $lang->action->label->deleted;
$lang->action->search->label['deletedfile']           = $lang->action->label->deletedfile;
$lang->action->search->label['editfile']              = $lang->action->label->editfile;
$lang->action->search->label['erased']                = $lang->action->label->erased;
$lang->action->search->label['undeleted']             = $lang->action->label->undeleted;
$lang->action->search->label['hidden']                = $lang->action->label->hidden;
$lang->action->search->label['commented']             = $lang->action->label->commented;
$lang->action->search->label['activated']             = $lang->action->label->activated;
$lang->action->search->label['blocked']               = $lang->action->label->blocked;
$lang->action->search->label['resolved']              = $lang->action->label->resolved;
$lang->action->search->label['reviewed']              = $lang->action->label->reviewed;
$lang->action->search->label['moved']                 = $lang->action->label->moved;
$lang->action->search->label['confirmed']             = $lang->action->label->confirmed;
$lang->action->search->label['bugconfirmed']          = $lang->action->label->bugconfirmed;
$lang->action->search->label['tostory']               = $lang->action->label->tostory;
$lang->action->search->label['frombug']               = $lang->action->label->frombug;
$lang->action->search->label['totask']                = $lang->action->label->totask;
$lang->action->search->label['svncommited']           = $lang->action->label->svncommited;
$lang->action->search->label['gitcommited']           = $lang->action->label->gitcommited;
$lang->action->search->label['linked2plan']           = $lang->action->label->linked2plan;
$lang->action->search->label['unlinkedfromplan']      = $lang->action->label->unlinkedfromplan;
$lang->action->search->label['changestatus']          = $lang->action->label->changestatus;
$lang->action->search->label['marked']                = $lang->action->label->marked;
$lang->action->search->label['linked2project']        = $lang->action->label->linked2project;
$lang->action->search->label['unlinkedfromproject']   = $lang->action->label->unlinkedfromproject;
$lang->action->search->label['linked2execution']      = $lang->action->label->linked2execution;
$lang->action->search->label['unlinkedfromexecution'] = $lang->action->label->unlinkedfromexecution;
$lang->action->search->label['started']               = $lang->action->label->started;
$lang->action->search->label['restarted']             = $lang->action->label->restarted;
$lang->action->search->label['recordestimate']        = $lang->action->label->recordestimate;
$lang->action->search->label['editestimate']          = $lang->action->label->editestimate;
$lang->action->search->label['canceled']              = $lang->action->label->canceled;
$lang->action->search->label['finished']              = $lang->action->label->finished;
$lang->action->search->label['paused']                = $lang->action->label->paused;
$lang->action->search->label['verified']              = $lang->action->label->verified;
$lang->action->search->label['login']                 = $lang->action->label->login;
$lang->action->search->label['logout']                = $lang->action->label->logout;
