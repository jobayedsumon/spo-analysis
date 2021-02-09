document.addEventListener('DOMContentLoaded', function () {

    categories = new Array();
    nameData = new Array();
    seriesData = new Array();


    $.each(orderDelivery, function (index, data) {

        singleData = new Array();

        categories.push(data.OrderDate);
        singleData.push(data.first_day_delivery);
        singleData.push(data.second_day_delivery);
        singleData.push(data.third_day_delivery);
        singleData.push(data.fourth_day_delivery);

        console.log(singleData)

        seriesData.push({
            name: '',
            data: singleData
        });

    });

    Highcharts.chart('container', {
        chart: {
            type: 'bar'
        },
        title: {
            text: 'Stacked bar chart'
        },
        xAxis: {
            categories: ['1feb', '2feb', '3feb', '4feb', '5feb']
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Total fruit consumption'
            }
        },
        legend: {
            reversed: false
        },
        plotOptions: {
            series: {
                stacking: 'normal'
            }
        },
        series: [{
            name: '1st day',
            data: [5, 3, 4, 7, 2]
        }, {
            name: '2nd day',
            data: [2, 2, 3, 2, 1]
        }, {
            name: '3rd day',
            data: [3, 4, 4, 2, 5]
        }]
    });

});
