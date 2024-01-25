function changeParams () {
  var week = $('#week').val()
  var product = $('#project').val()
  var user = $('#user').val()
  var link = createLink('projectweekly', 'weeklyboard', 'week=' + week + '&product=' + product + '&user=' + user);
  location.href = link;
}