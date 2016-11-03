$(document).ready(function () {

    var datasets = [];

    var labels = [];
    var dataset1 = {data:[], label: 'expense', backgroundColor: 'red'};
    $.each(listSumByMonth, function(i, row) {
        labels.push(row.date);
        dataset1.data.push(row.amount);
    });
    var dataset2 = {data:[], 'label' : 'income', backgroundColor: 'blue'};

    $.each(listIncomeByMonth, function(i, row) {
        dataset2.data.push(row.amount);
    });

    datasets.push(dataset1);
    datasets.push(dataset2);

    Utilities.createChart2(datasets, labels);
});