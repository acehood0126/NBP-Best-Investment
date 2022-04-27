# nbp best investment

Simple application to calculate the best investment in gold during given time period.

It depends on NBP Web Api “http://api.nbp.pl” and returns the best moment to buy and sell gold to get the highest profit.

##### Usage:

`php calculate.php [money] [startDate] [endDate]`

##### Parameters:

`money` - amount of money to invest, float value,  
`startDate` - time range start date in format Y-m-d,  
`endDate` - time range end date in format Y-m-d.

##### Example of usage:

`php calculate.php 600000 2013-01-02 2017-08-28`
