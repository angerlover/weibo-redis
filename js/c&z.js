// 赞
	function zan(a)
	{
		var zan = $(a);
		var postid = zan.attr('postid'); // 微博id
		var f = zan.attr('f'); // 赞的状态
		/* Act on the event */
		event.preventDefault(); // 阻止默认事件
		$.ajax({
			url:'zan.php?postid='+postid+'&f='+f,
			type:'GET',
			dataType:'json',
			success:function(data){
				if(data.error==0)
				{
					console.log(data);
					zan.attr('f',data.now_status);
					zan.find('span:eq(1)').html(data.count);
					if(data.count == 0)
					{
						zan.find('span:eq(1)').html('');
					}
					if(data.now_status == 0)
					{
						zan.find('span:eq(0)').css('color','red');
					}
					else
					{
						zan.find('span:eq(0)').css('color','');
						
					}
				}
			},

		});
	}


	<!-- 收藏 -->


	function collect(a)
	{
		var collect = $(a);
		var postid = collect.attr('postid'); // 微博id
		var f = collect.attr('f'); // 收藏的状态
		/* Act on the event */
		event.preventDefault(); // 阻止默认事件
		$.ajax({
			url:'collect.php?postid='+postid+'&f='+f,
			type:'GET',
			dataType:'json',
			success:function(data){
				if(data.error==0)
				{
					console.log(data);
					collect.attr('f',data.now_status);
					if(data.now_status == 0)
					{
						collect.find('span:eq(0)').text('已收藏');
						collect.find('span:eq(1)').removeClass();
						collect.find('span:eq(1)').addClass('glyphicon glyphicon-heart');
						
					}
					else
					{
						collect.find('span:eq(0)').text('收藏');
						collect.find('span:eq(1)').removeClass();
						collect.find('span:eq(1)').addClass('glyphicon glyphicon-heart-empty');
						
					}
				}
			},

		});

	}