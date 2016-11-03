Utilities = {};

Utilities.createChart = function (labels, values, colors, title) {

    var ctx = $('<canvas width="400" height="100"></canvas>').appendTo('body');

    return new Chart(ctx, {
        type: 'bar',
        label: 'toto',
        data: {
            labels: labels,
            datasets: [{
                label: title,
                data: values,
                backgroundColor: colors
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


Utilities.createChart2 = function (datasets, labels) {

    var ctx = $('<canvas width="400" height="100"></canvas>').appendTo('body');

    console.log(labels);
    console.log(datasets);

    return new Chart(ctx, {
        type: 'bar',
        label: 'toto',
        data: {
            labels: labels,
            datasets: datasets
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
Utilities.getDatastore = function (key) {
    return datastore[key] ? datastore[key] : null;
}

Utilities.getCategoryColor = function (categorySlug) {
    var mapping = this.getDatastore('categoryColors');

    return mapping[categorySlug] ||  null;
}