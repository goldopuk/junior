$(document).ready(function () {

    function createChart(labels, values, title) {

        var ctx = $('<canvas width="400" height="100"></canvas>').appendTo('body');

        return new Chart(ctx, {
            type: 'bar',
            label: 'toto',
            data: {
                labels: labels,
                datasets: [{
                    label: title,
                    data: values
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero:true
                        }
                    }]
                }
            }
        });
    }

    // data inhtml
    if ( ! allByMonthData) {
        alert('missing data');
    }

    var labels = [];
    var values = [];



    $.each(allByMonthData, function(i, row) {
        labels.push(row.date);
        values.push(row.amount);
    });

    createChart(labels, values);

    // data inhtml
    if ( ! monthByMonthData) {
        alert('missing data');
    }

    $.each(monthByMonthData, function(date, dataset) {

        labels = [];
        values = [];

        var title = "Mês " + date;

        $.each(dataset,  function(i, row) {
            labels.push(row.subcategory);
            values.push(row.amount);

        });

        //createChart(labels, values, title);

    });

    $.each(monthByMonthByCategoryData, function(date, dataset) {

        labels = [];
        values = [];

        var title = "Mês " + date;

        $.each(dataset,  function(i, row) {
            labels.push(row.category);
            values.push(row.amount);

        });

        createChart(labels, values, title);

    });


});