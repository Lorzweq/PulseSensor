<!DOCTYPE html> 
<html> 
<head> 
  <meta http-equiv="refresh" content="2"> 
  <style> 
    #heart { 
      position: relative; 
      width: 100px; 
      height: 90px; 
      margin-top: 100px; 
      margin-left: 100px; 
      animation: heartbeat 1s infinite; 
    }

    #heart::before, #heart::after { 
      content: ""; 
      position: absolute; 
      top: 0; 
      width: 52px; 
      height: 80px; 
      border-radius: 50px 50px 0 0; 
      background: red; 
    }

    #heart::before { 
      left: 50px; 
      transform: rotate(-45deg); 
      transform-origin: 0 100%; 
    }

    #heart::after { 
      left: 0; 
      transform: rotate(45deg); 
      transform-origin: 100% 100%; 
    }

    @keyframes heartbeat { 
      0% { 
        transform: scale(1); 
      } 
      8% { 
        transform: scale(0.7); 
      } 
      17% { 
        transform: scale(1.5); 
      } 
      25% { 
        transform: scale(0.7); 
      } 
      85.5% { 
        transform: scale(0.7); 
      } 
      100% { 
        transform: scale(1); 
      } 
    }

    #kaavio-container { 
      width: 1000px; 
      height: 500px; 
      margin-top: 10%; 
    }
  </style> 

  <title>Pulssi</title> 

  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script> 
  <script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>  

  <script type="text/javascript"> 
    google.charts.load('current', { 'packages': ['corechart'] }); 
    google.charts.setOnLoadCallback(init); 

    var dataTable; 
    var chart; 
    var options = { 
      title: 'Pulssikäyrä', 
      curveType: 'function', 
      legend: { position: 'bottom' }, 
      hAxis: { title: 'Päivä', slantedText: true, slantedTextAngle: 45 }, 
      seriesType: 'line', 
      series: { 1: { type: 'line' } }, 
      width: 2000, 
      height: 500, 
      chartArea: { width: '80%', height: '70%' }, 
      pointSize: 5, 
    };
    // Initialize chart
    function init() { 
      chart = new google.visualization.LineChart(document.getElementById('kaavio')); 
      dataTable = new google.visualization.DataTable(); 
      dataTable.addColumn('string', 'PvmAika'); 
      dataTable.addColumn('number', 'Pulssi'); 
      fetchData(); 
    }
    // Fetch data from database with fetch_data.php
    function fetchData() { 
      $.ajax({ 
        url: 'fetch_data.php', 
        success: function (data) { 
          var dataArray = JSON.parse(data); 
          dataArray.forEach(row => dataTable.addRow(row)); 
          
          if (dataTable.getNumberOfRows() > 20) { 
            dataTable.removeRow(0); 
          }

          chart.draw(dataTable, options); 
          setTimeout(fetchData, 2000);  
        } 
      }); 
    }

    function updateHeartAnimation(bpm) { 
      var heart = document.getElementById('heart'); 
      var animationDuration = 60 / bpm;  
      heart.style.animationDuration = animationDuration + 's';  
    }
  </script> 
</head> 

<body> 
  <div id="heart"></div> 
  <br /><br /> 
  <div id="kaavio-container"> 
    <div id="kaavio" style="width:2000px; height:800px; margin-left:100px;"></div> 
  </div> 
</body> 
</html>
