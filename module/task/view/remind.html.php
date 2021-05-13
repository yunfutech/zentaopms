<?php $mailTitle = '禅道任务安排不合理员工名单'?>
<?php include $this->app->getModuleRoot() . 'common/view/mail.header.html.php';?>
<tr>
  <td style='padding: 10px; border: none;'>
    <fieldset style='border: 1px solid #e5e5e5'>
      <legend style='color: #114f8e'>禅道任务安排不合理员工名单</legend>
      <div style='padding:5px;'><?php echo $summary;?></div>
    </fieldset>
  </td>
</tr>
<tr>
  <td style='padding: 10px; border: none;'>
    <fieldset style='border: 1px solid #e5e5e5'>
      <legend style='color: #114f8e'>延期任务排行榜</legend>
      <div style='padding:5px;'>
        <table>
          <tr>
            <th width="100">姓名</th>
            <th>数量</th>
            <th width="50">运动</th>
          </tr>
          <?php foreach($deleyTasksRank as $value):?>
          <tr>
            <td><?php echo $value['name'];?></td>
            <td class='text-center'><?php echo $value['delay_count'];?></td>
            <td><?php echo $value['train_count'];?></td>
          </tr>
          <?php endforeach;?>
        </table>
      </div>
    </fieldset>
  </td>
</tr>
<!-- <tr>
  <td style='padding: 10px; border: none;'>
    <fieldset style='border: 1px solid #e5e5e5'>
      <legend style='color: #114f8e'>未提交日报名单</legend>
      <div style='padding:5px;'>
        <table>
          <tr>
            <th width="50">姓名</th>
            <th width="50">运动</th>
          </tr>
          <?php foreach($uncommittedUsers as $user):?>
          <tr>
            <td><?php echo $user->realname;?></td>
            <td><?php echo 10;?></td>
          </tr>
          <?php endforeach;?>
        </table>
      </div>
    </fieldset>
  </td>
</tr> -->
<tr>
  <td style='padding: 10px; border: none;'>
    <fieldset style='border: 1px solid #e5e5e5'>
      <legend style='color: #114f8e'>迭代延期排行榜</legend>
      <div style='padding:5px;'>
        <table>
          <tr>
            <th width="100">项目经理</th>
            <th>数量</th>
            <th width="50">运动</th>
            <th>详情</th>
          </tr>
          <?php foreach($delayProjects as $value):?>
          <tr>
            <td><?php echo $value->realname;?></td>
            <td class='text-center'><?php echo $value->cnt;?></td>
            <td><?php echo '+' . strval(intval($value->cnt) * 50);?></td>
            <td><?php echo $value->projects;?></td>
          </tr>
          <?php endforeach;?>
        </table>
      </div>
    </fieldset>
  </td>
</tr>
<tr>
  <td style='padding: 10px; border: none;'>
    <fieldset style='border: 1px solid #e5e5e5'>
      <div style='padding:5px;'>
        <b>注意：</b><br/>
        1. 当天任务不饱和运动+20<br/>
        2. 每存在一个未及时处理的任务运动+10<br/>
        3. 每个延期的迭代运动+20
      </div>
    </fieldset>
  </td>
</tr>
<?php include $this->app->getModuleRoot() . 'common/view/mail.footer.html.php';?>
<style type="text/css">
  table
  {
      border-collapse: collapse;
  }
  table td, table th
  {
      border: 1px solid #cad9ea;
      color: #666;
      height: 30px;
  }
  table thead th
  {
      background-color: #CCE8EB;
      width: 100px;
  }
  table tr:nth-child(odd)
  {
      background: #fff;
  }
  table tr:nth-child(even)
  {
      background: #F5FAFA;
  }
  .text-center {
    text-align: center;
    margin: 0 auto;
  }
</style>