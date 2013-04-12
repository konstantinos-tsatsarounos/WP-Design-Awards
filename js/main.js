jQuery(document).ready(function($){



    $('div.rating').jRating();

    $('.rating-from-post').jRating();    


    $('.rating-from-post').css({

       'background-color' : main_var.inactive_star_color

    });

    $('.jRatingColor').css({

       'background-color' : main_var.average_rating_color

    });

    $('.jRatingAverage').css({

       'background-color' : main_var.active_rating_color

    });

    

    var postBoxHeight = $('.aw_post').height(),

        postBoxWidth = $('.aw_post').width(),

        SmallPostBoxHeight = $('.aw_post').eq(1).height(),

        SmallPostBoxWidth = $('.aw_post').eq(1).width(),

        buttonsHeight = $('.aw_buttons').height(),

        buttonsWidth = $('.aw_buttons').width(),

        height = $('.rating-box').height();        

  

       

    $('.aw_post .cover').css({ 'opacity' : 0 });





    $('.rating-stats').css({

        'position': 'absolute',

        'bottom' : 0,

        'right' : 0

    });   

  



    $('.aw_post')        

        .mouseenter(function(){           

            

            $(this).children('.title').delay(200).fadeIn(200);

            $(this).children('.cover').animate({

                'opacity' :.5

            },200);

            

            $(this).children('.rating-box').animate({ 'bottom' : 0},300);

            $(this).children('.rating-stats').delay(100).animate({'bottom' : height+10 },600);

            $(this).children('.aw_buttons')       

                .fadeIn(600);

        })

        .mouseleave(function(){



            $(this).children('.title').fadeOut(200);

            $(this).children('.cover').animate({

                'opacity' :0

            },200);



            $(this).children('.rating-box').animate({ 'bottom' : -100},100);

            $('.rating-stats').animate({'bottom' : 0 },90);

            $(this).children('.aw_buttons').fadeOut(300);

        });

        

            $('.aw_buttons').css({

                    'position' : 'absolute',

                    'top' : (SmallPostBoxHeight-buttonsHeight)/1.8,

                    'left' : ((SmallPostBoxWidth-buttonsWidth)/2)

                });

            $('.aw_buttons').eq(0).css({

                'position' : 'absolute',

                'top' : (postBoxHeight-buttonsHeight)/1.8,

                'left' : ((postBoxWidth-buttonsWidth)/2)

            });



    if(window.openDatabase){
    	var db = openDatabase('aw_design_awards',1,'aw_design_awards',10000000);
    	db;
    }

    var db = false;
    if(db){

        db.transaction(function(transaction){

            var host = window.location.host;



            transaction.executeSql("select postID from design_awards_data where site=?",[host],function(transaction,results){

                for (var i=0; i < results.rows.length; i++){



                    row = results.rows.item(i);

                    postID = row.postID;

                    disableVotedSites(postID);

                }

            });

        });

    }

    if(store){
    	var daw = store.get('daw_');
    	if( typeof(daw) == 'string' && typeof(JSON.parse(daw)) == 'object'){
        	daw = JSON.parse(daw);
        	
        	for(var ii = 0;ii<daw.length; ii++){
        		disableVotedSites(daw[ii]);
        	}
        }
    }
    else{
    	console.log('store not working');
    }

    function disableVotedSites(id)

    {



        var site = $('div[data-id='+id+']');

        $(site).parent('.rating-box').css({
            opacity: 0
        });



        $(site)

            .parent('.rating-box')

            .parent('.aw_post')

            .unbind('mouseenter')

            .unbind('mouseleave')

            .mouseenter(function(){



                $(this).children('.title').delay(200).fadeIn(200);

                $(this).children('.cover').animate({

                    'opacity' :.5

                },200);



                $(this).children('.aw_buttons').fadeIn(600);

            })

            .mouseleave(function(){



                $(this).children('.title').fadeOut(200);

                $(this).children('.cover').animate({

                    'opacity' :0

                },200);



                $(this).children('.aw_buttons').fadeOut(300);

            })
            .children('.rating-box').html('<h5>You have already voted!</h5>');

    }

   

  

});

