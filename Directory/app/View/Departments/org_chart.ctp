<script type='text/javascript' src='https://www.google.com/jsapi'></script>
    <script type='text/javascript'>
      google.load('visualization', '1', {packages:['orgchart']});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Department/Unit');
        data.addColumn('string', 'Parent');
        data.addColumn('string', 'ToolTip');
        data.addRows([
         <?php foreach ($departments as $department){
         	echo "['".addslashes($department['Department']['name'])."', '".(!empty($department['Department_above']['name'])?addslashes($department['Department_above']['name']):'')."',''],\n";
         }?>         
        ]);
        var chart = new google.visualization.OrgChart(document.getElementById('chart_div'));
        chart.draw(data, {allowHtml:true});
      }
    </script>
<div id='chart_div'>
</div>
