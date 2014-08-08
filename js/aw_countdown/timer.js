var AwCountdown = Class.create({
    initialize: function(format,time,container) {
        this.dateTo = time;
        this.container = container;
        this.format = format;
        this.getCounter();
    },
    addZero: function(str){
        if(str<10){
            return '0'+str;
        }
        return str;
    },
    getCounter: function() {
        amount=this.dateTo;
        if(amount < 0){
            window.location.href=controllerUrl;
            return;
        }
        days=Math.floor(amount/86400);
        amount=amount%86400;
        hours=Math.floor(amount/3600);
        amount=amount%3600;
        mins=Math.floor(amount/60);
        amount=amount%60;
        secs=Math.floor(amount);
        tmp = $(this.container).getElementsByClassName('aw_countdown_timer');
        timer_container = tmp[0];
        tmp = timer_container.getElementsByClassName('aw_countdown_days_container');
        days_container = tmp[0];
        tmp = timer_container.getElementsByClassName('aw_countdown_hours_container');
        hours_container = tmp[0];
        tmp = timer_container.getElementsByClassName('aw_countdown_separator_afterhours');
        afterhours_separator = tmp[0];
        tmp = timer_container.getElementsByClassName('aw_countdown_minutes_container');
        mins_container = tmp[0];
        tmp = timer_container.getElementsByClassName('aw_countdown_separator_afterminutes');
        aftermins_separator = tmp[0];
        tmp = timer_container.getElementsByClassName('aw_countdown_seconds_container');
        secs_container = tmp[0];
        days_container.down(2).update(this.addZero(days));
        if(this.format.indexOf('H')!=-1){
            hours_container.down(2).update(this.addZero(hours));
        }else{
            hours_container.style.display = 'none';
        }
        if(this.format.indexOf('M')!=-1){
            mins_container.down(2).update(this.addZero(mins));
        }else{
            mins_container.style.display = 'none';
            afterhours_separator.style.display = 'none';
        }
        if(this.format.indexOf('S')!=-1){
            secs_container.down(2).update(this.addZero(secs));
        }else{
            secs_container.style.display = 'none';
            aftermins_separator.style.display = 'none';
        }
        this.dateTo = this.dateTo -1;
        setTimeout(function(){
            this.getCounter();
        }.bind(this), 1000);
    }
});
 