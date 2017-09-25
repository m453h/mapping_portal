<?php

namespace AppBundle\Helpers;

class ChartBuilder
{

	private $chartTitle='';
	private $xAxisLabel='';
	private $yAxisLabel='';
	private $chartType='';
	private $xData;
	private $yData=[];
	private $myChart;
	private $legendLabels=[];
	private $yDataSets;
	private $xDataSets;

	private $permissions = [];

	public function __construct()
	{

	}


	public function setPermissions($value)
	{
		$this->permissions = $value;
	}

	public function renderChartView()
	{

		$content='<div class=\'col12 drop-shadow-2 content\'>
							
							
							<div class=\'section-outer\'>
								
								<div class=\'col12 space\'></div>
								
								<div class=\'ct-chart col12\'></div>
									<div class=\'col12\'>
										<div class=\'container\'>
												<div id="main"  style="width:100%; min-height: 700px;"></div>
												'.$this->myChart.'
										</div>
								</div>
								
								<div class=\'col12 space\'></div>

							</div>
				  </div>';

		return $content;
	}

	public function setChartType($chartType)
	{
		$this->chartType = $chartType;
	}

	public function setYAxisLabel($yAxisLabel)
	{
		$this->yAxisLabel = $yAxisLabel;
	}

	public function setXAxisLabel($xAxisLabel)
	{
		$this->xAxisLabel = $xAxisLabel;
	}

	public function setChartTitle($chartTitle)
	{
		$this->chartTitle = $chartTitle;
	}

	public function setYData($yData)
	{
		$this->yData = $yData;
	}

	public function setXData($xData)
	{
		$this->xData = $xData;
	}

	public function generateLineGraph()
	{

		$content=$this->renderChartView();

		$content.='<script>
				Chart.defaults.global.legend.display = false;
			
				var chartData = {
					labels: ['.$this->xDataSets.'],
					datasets: ['.rtrim($this->yDataSets,',').']
				};	
				window.onload = function(){
					var ctx = document.getElementById("canvas").getContext("2d");
					var myLineChart = new Chart(ctx, {
						type: "line",
					data: chartData,
					options: {
							scales: {
										xAxes: [{
													stacked: false,	
												}],
										yAxes: [{
												stacked: false
										}]},
										
							  tooltips: {
                 						   mode: \'label\',
                    					   callbacks: { }
               	                        },
								hover: {
									mode: \'dataset\'
								}			
							
						}
				});
				
				}
			</script>';

		return $content;
	}
	
	public function setLineGraphData($dataLabel,$data,$extra=array('bgColor'=>null,'defColor'=>null))
	{

		$this->yDataSets .= '{
							label: "'.$dataLabel.'",
							fill: true,
							lineTension: 0.1,
							backgroundColor: "'.$extra['bgColor'].'",
							borderColor: "'.$extra['defColor'].'",
							borderCapStyle: "butt",
							borderDash: [],
							borderDashOffset: 0.0,
							borderJoinStyle: "miter",
							pointBorderColor: "'.$extra['defColor'].'",
							pointBackgroundColor: "'.$extra['defColor'].'",
							pointBorderWidth: 1,
							pointHoverRadius: 8,
							pointHoverBackgroundColor: "'.$extra['defColor'].'",
							pointHoverBorderColor: "rgba(220,220,220,1)",
							pointHoverBorderWidth: 2,
							pointRadius: 5,
							pointHitRadius: 10,
							data: ['.$data.'],
						},';

	}

	public function setXDataSets($xDataSets)
	{
		$this->xDataSets = $xDataSets;
	}

	public function renderBasicEchart(){
		
		$yData = implode(',',$this->yData);

		$legend = implode(',',$this->legendLabels);


		$this->myChart='	
						<script type="text/javascript">
							// Initial Configuration Javascript Folder
							require.config({
												paths: {
													echarts: \'/web/vendor/echarts\'
												}
											});
							
							//Echarts configuration
							require(
									[
										\'echarts\',
										\'echarts/chart/line\',
										\'echarts/chart/bar\'
									],
							function (ec) 
							{
								// Find the chart in DOM by ID
								var myChart = ec.init(document.getElementById(\'main\')); 
							
								var option  = 
												{
													title : 
															{
																text: \''.$this->chartTitle.'\',
																subtext: \'\'

															},
													
													tooltip : 
													{
															trigger: \'axis\'
													},
													legend: 
													{
														data:['.$legend.'],y:\'top\'
													},
													toolbox: 
													{
															show : true,
															feature : {
															mark : {show: false},
															dataView : {show: false, readOnly: true},
															magicType : {show: true, type: [\'line\', \'bar\']},
															restore : {show: true},
															saveAsImage : {show: true}}
													},
							
													calculable : false,
												
													xAxis : [
																{
																	type : \'category\',
																	boundaryGap:false,
																	name : \''.$this->xAxisLabel.'\',
																	data : ['.$this->xData.'],
																	axisLabel: {
																					show: true,
																					interval: \'0\',
																					rotate: -40,
																					margin: 10,
																					clickable: false,
																					formatter: null,
																					textStyle: {
																								fontFamily: \'Arial, Verdana\',
																								fontSize: 11,
																								fontStyle: \'normal\',
																								fontWeight: \'normal\',
																								}
																					},
																}
															],
													
													yAxis : [
																{
																type : \'value\',
																name : \''.$this->yAxisLabel.'\'
																}
															],
													
													series : [
																'.$yData.'
															]
												};
							
								myChart.setOption(option); 
								myChart.setTheme(\'infographic\')
								window.onresize = function() {
									myChart.resize();
								};
							
							}
						);
						
						</script>';
		
	}

	public function setDataSeries($value,$name,$type)
	{
		$count = count($this->yData);

		$this->legendLabels[$count]='\''.$name.'\'';

		if($type=='line' || $type=='bar')
		{
			$this->yData[$count] ='{
									name:\''.$name.'\',
									type:\''.$type.'\',
									data:['.$value.']
								  }';
		}
		else
		{
			$this->yData[$count] ='{
									value:['.$value.'],
									name:\''.$name.'\',
								  }';

		}

	}

	public function renderPieEchart(){

		$yData = implode(',',$this->yData);

		$legend = implode(',',$this->legendLabels);

		$this->myChart='
						<script type="text/javascript">
							// Initial Configuration Javascript Folder
							require.config({
											paths: {
													echarts: \'/web/vendor/echarts\'
											}
							});
				
							//Echarts configuration
							require(
							[
								\'echarts\',
								\'echarts/chart/pie\', // Use Bar chart
								\'echarts/chart/funnel\' // Use Bar chart
							],
							function (ec) 
							{
								// Find the chart in DOM by ID
								var myChart = ec.init(document.getElementById(\'main\')); 
				
								var option  = 
												{
													title : {
														text: \''.$this->chartTitle.'\',
														subtext: \'\',
														x:\'center\'
													},
													tooltip : {
														trigger: \'item\',
														formatter: "{a} <br/>{b} : {c} ({d}%)"
													},
													legend: {
																orient : \'vertical\',
																x : \'left\',
																data:['.$legend.']

													},
													toolbox: 
													{
														show : true,
														feature : {
																	mark : {show: false},
																	dataView : {show: false, readOnly: false},
																	magicType : {
																					show: true, 
																					type: [\'pie\'],
																					option: {
																					funnel: {
																								x: \'25%\',
																								width: \'50%\',
																								funnelAlign: \'left\',
																								max: 100
																							}
																					}
																				},
																	restore : {show: true},
																	saveAsImage : {show: true}
																}
													},
													calculable : true,
													series : [
																{
																	name:\'Market Share\',
																	type:\'pie\',
																	radius : \'55%\',
																	 itemStyle : {
																		normal : {
																			label : {
																				position : \'outer\',
																				formatter : "{b}\n{d}%"
																			},
																			labelLine : {
																				show : true
																			}
																		},
																		emphasis : {
																			label : {
																				show : false
																			}
																		}
																		
																	},
																	center: [\'50%\', \'60%\'],
																	data:	[
																				'.$yData.'
																			],
																			
																	
																			
																}
																
															]
												};
				
				
				
								myChart.setOption(option); 
								myChart.setTheme(\'infographic\')
								window.onresize = function() {
								myChart.resize();
								};
				
							}
							);
				
						</script>';

	}
	
	
	
	
	
	
	
}