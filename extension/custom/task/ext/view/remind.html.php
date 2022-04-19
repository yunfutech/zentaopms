<?php $mailTitle = '禅道任务安排不合理员工名单' ?>
<?php include $this->app->getModuleRoot() . 'common/view/mail.header.html.php'; ?>
<tr>
  <td style='padding: 10px; border: none;'>
    <fieldset style='border: 1px solid #e5e5e5'>
      <legend style='color: #114f8e'>禅道任务安排不合理员工名单</legend>
      <div style='padding:5px;'><?php echo $summary; ?></div>
    </fieldset>
  </td>
</tr>
<tr>
  <td style='padding: 10px; border: none;'>
    <fieldset style='border: 1px solid #e5e5e5'>
      <legend style='color: #114f8e'>延期任务排行榜</legend>
      <div style='padding:5px;'>
        <table class="count">
          <tr>
            <th width="50">序号</th>
            <th width="100">姓名</th>
            <th width="70">数量</th>
            <th width="60">运动</th>
          </tr>
          <?php foreach ($deleyTasksRank as $key => $value) : ?>
            <tr>
              <td class="text-center"><?php echo $key + 1; ?></td>
              <td><?php echo $value['name']; ?></td>
              <td class='text-center'><?php echo $value['delay_count']; ?></td>
              <td><?php echo $value['train_count']; ?></td>
            </tr>
          <?php endforeach; ?>
        </table>
      </div>
    </fieldset>
  </td>
</tr>
<tr>
  <td style='padding: 10px; border: none;'>
    <fieldset style='border: 1px solid #e5e5e5'>
      <legend style='color: #114f8e'>迭代延期排行榜</legend>
      <div style='padding:5px;'>
        <table class="count">
          <tr>
            <th width="50">序号</th>
            <th width="100">项目经理</th>
            <th width="70">数量</th>
            <th width="60">运动</th>
            <th width="300">详情</th>
          </tr>
          <?php foreach ($delayProjects as $key => $value) : ?>
            <tr>
              <td class="text-center"><?php echo $key + 1; ?></td>
              <td><?php echo $value->realname; ?></td>
              <td class='text-center'><?php echo $value->cnt; ?></td>
              <td><?php echo '+' . strval(intval($value->cnt) * $config->exerciseNum->delayProject); ?></td>
              <td><?php echo $value->projects; ?></td>
            </tr>
          <?php endforeach; ?>
        </table>
      </div>
    </fieldset>
  </td>
</tr>
<tr>
  <td style='padding: 10px; border: none;'>
    <fieldset style='border: 1px solid #e5e5e5'>
      <legend style='color: #114f8e'>待处理bug按用户统计</legend>
      <div style='padding:5px;'>
        <table class="count">
          <tr>
            <th width="50">序号</th>
            <th width="100">员工</th>
            <th width="80">待处理个数</th>
          </tr>
          <?php foreach ($countUserBugs as $key => $user) : ?>
            <?php if (trim($user->userName != '')) : ?>
              <tr>
                <td class="text-center"><?php echo $key + 1; ?></td>
                <td><?php echo $user->userName; ?></td>
                <td><?php echo $user->bugNum; ?></td>
              </tr>
            <?php endif; ?>
          <?php endforeach; ?>
        </table>
      </div>
    </fieldset>
  </td>
</tr>
<tr>
  <td style='padding: 10px; border: none;'>
    <fieldset style='border: 1px solid #e5e5e5'>
      <legend style='color: #114f8e'>待解决bug</legend>
      <div style='padding:5px;'>
        <table class="count">
          <tr>
            <th width="50">序号</th>
            <th width="200">项目</th>
            <th width="200">迭代</th>
            <th width="300">bug标题</th>
            <th width="100">指派给</th>
            <th width="100">激活时长(天)</th>
          </tr>
          <?php foreach ($unresolvedBugs as $key => $bug) : ?>
            <tr>
              <td class="text-center"><?php echo $key + 1; ?></td>
              <td><?php echo $bug->projectName; ?></td>
              <td><?php echo $bug->executionName; ?></td>
              <td><?php echo $bug->title; ?></td>
              <td><?php echo $bug->userName; ?></td>
              <td><?php echo $bug->dateDiff; ?></td>
            </tr>
          <?php endforeach; ?>
        </table>
      </div>
    </fieldset>
  </td>
</tr>
<tr>
  <td style='padding: 10px; border: none;'>
    <fieldset style='border: 1px solid #e5e5e5'>
      <div style='padding:5px;'>
        <b>注意：</b><br />
        1. 当天任务不饱和运动+20<br />
        2. 每存在一个未及时处理的任务运动+10<br />
        3. 每个延期的迭代运动+20
      </div>
    </fieldset>
  </td>
</tr>
<?php include $this->app->getModuleRoot() . 'common/view/mail.footer.html.php'; ?>
<style type="text/css">
  table {
    border-collapse: collapse;
  }

  table td,

  table th {
    border: 1px solid #cad9ea;
    color: #666;
    height: 30px;
  }

  table thead th {
    background-color: #CCE8EB;
    width: 100px;
  }

  table tr:nth-child(odd) {
    background: #fff;
  }

  table tr:nth-child(even) {
    background: #F5FAFA;
  }

  .text-center {
    text-align: center;
    margin: 0 auto;
  }

  .count td {
    padding-left: 10px;
  }
</style>