$(function()
{
	var $dropdowns = $('.dropdown-enabled'); // Specifying the element is faster for older browsers
	
	$dropdowns.on('mouseover', function()
	{
		var $this = $(this);
		var this_id = $this.attr("id").substring(7);
		
		if ($this.prop('hoverTimeout'))
		{
			$this.prop('hoverTimeout', clearTimeout($this.prop('hoverTimeout')));
		}
		
		$this.prop('hoverIntent', setTimeout(function()
		{
			$this.addClass('active');
			$("#" + this_id + "-dropdown").show();
		}, 250));
	})
	.on('mouseleave', function()
	{
		var $this = $(this);
		var this_id = $this.attr("id").substring(7);

		if ($this.prop('hoverIntent'))
		{
			$this.prop('hoverIntent', clearTimeout($this.prop('hoverIntent')));
		}

		$this.prop('hoverTimeout', setTimeout(function()
		{
			$("#" + this_id + "-dropdown").hide();
			$this.removeClass('active');
		}, 250));
	});
	
	if ('ontouchstart' in document.documentElement)
	{
		$dropdowns.each(function()
		{
			var $this = $(this);

			this.addEventListener('touchstart', function(e)
			{
				if (e.touches.length === 1)
				{
					// Prevent touch events within dropdown bubbling down to document
					e.stopPropagation();

					// Toggle hover
					if (!$this.hasClass('hover'))
					{
						// Prevent link on first touch
						if (e.target === this || e.target.parentNode === this)
						{
							e.preventDefault();
						}

						// Hide other open dropdowns
						$dropdowns.removeClass('hover');
						$this.addClass('hover');

						// Hide dropdown on touch outside
						document.addEventListener('touchstart', closeDropdown = function(e)
						{
							e.stopPropagation();

							$this.removeClass('hover');
							document.removeEventListener('touchstart', closeDropdown);
						});
					}
				}
			}, false);
		});
	}
});

$(function()
{
	var $dropdowns = $('#button-profile'); // Specifying the element is faster for older browsers
	
	$dropdowns.on('mouseover', function()
	{
		var $this = $(this);
		
		if ($this.prop('hoverTimeout'))
		{
			$this.prop('hoverTimeout', clearTimeout($this.prop('hoverTimeout')));
		}
		
		$this.prop('hoverIntent', setTimeout(function()
		{
			$("#user-nav-wrapper").show();
		}, 250));
	})
	.on('mouseleave', function()
	{
		var $this = $(this);
		var this_id = $this.attr("id").substring(7);

		if ($this.prop('hoverIntent'))
		{
			$this.prop('hoverIntent', clearTimeout($this.prop('hoverIntent')));
		}

		$this.prop('hoverTimeout', setTimeout(function()
		{
			if($('#username').is(':focus') || $('#password').is(':focus'))
			{
return ; // search field is focused, ignore other part of function
			}
			else
			{
				$("#user-nav-wrapper").hide();
			}
		}, 250));
	});
	
	if ('ontouchstart' in document.documentElement)
	{
		$dropdowns.each(function()
		{
			var $this = $(this);

			this.addEventListener('touchstart', function(e)
			{
				if (e.touches.length === 1)
				{
					// Prevent touch events within dropdown bubbling down to document
					e.stopPropagation();

					// Toggle hover
					if (!$this.hasClass('hover'))
					{
						// Prevent link on first touch
						if (e.target === this || e.target.parentNode === this)
						{
							e.preventDefault();
						}

						// Hide other open dropdowns
						$("#user-nav-wrapper").hide();
						$this.addClass('hover');

						// Hide dropdown on touch outside
						document.addEventListener('touchstart', closeDropdown = function(e)
						{
							e.stopPropagation();

							document.removeEventListener('touchstart', closeDropdown);
						});
					}
				}
			}, false);
		});
	}
});
$(document).ready(function() {
			
	$("#q").focus(function() {
		if($("#q").val() == "Search")
		{
			$("#q").val("");
		}
		$("#q").css("color", "#2C71AE");
	});

	$("#q").blur(function() {
		if($("#q").val() == "")
		{
			$("#q").val("Search");
			$("#q").css("color", "#bfbfbf");
			$(".search-result-row").html('<div class="opensans fourteenfont text-color1" align="center">Search results will go here.</div>');
		}
		$("#searchresults").hide();
	});
		
	$('.search-box').on('focus', function()
	{
		
		var $this = $(this);
		var this_id = $this.attr("id").substring(7);
		
		if ($this.prop('hoverTimeout'))
		{
			$this.prop('hoverTimeout', clearTimeout($this.prop('hoverTimeout')));
		}
		
		$this.prop('hoverIntent', setTimeout(function()
		{
			$("#searchresults").show();
		}, 250));
	})
	$('#searchresults').on('mouseleave', function()
	{
		if($('.search-box').is(":focus"))
		{
		}
		else
		{
			var $this = $(this);

			if ($this.prop('hoverIntent'))
			{
				$this.prop('hoverIntent', clearTimeout($this.prop('hoverIntent')));
			}

			$this.prop('hoverTimeout', setTimeout(function()
			{
				$("#searchresults").hide();
			}, 250));
		}
	});
	
	
	var textObj = new Object();
	$(".search-box").on("keyup", function(){
		var this_id = $(this).attr("id");
		// declare object level vars
		textObj[this_id] = "";
		// check if the object is set for the count
		if (typeof textObj[this_id + "count"] === "undefined")
		{
			textObj[this_id + "count"] = 0; // we will need this later..
		}
		else
		{
			// its set, lets up it by one.
			textObj[this_id + "count"]++;
		}
		
		// by default we clear the previous timer, if there is one.
		clearTimeout(textObj[this_id + "count"] - 1);
		
		// set the timer for the current count
		textObj[this_id + "count"] = setTimeout(function() {
			// declare the date objects
			var d = new Date();
			$.ajax({
				type: "POST",
				url: "/v6/ajax.php?action=search",
				data: $('#q').serialize(),
				success: function(html) {
					$("#inner-search-results").html(html);
				}
			//$(".search-result-row").html('<div class="opensans fourteenfont text-color1" align="center">Search results will go here.</div>');
			// add a check if it's empty to revert it back and not query the database.
			});
			return false;
		}, 500);
	});
});