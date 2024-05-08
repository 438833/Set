document.addEventListener(
	"DOMContentLoaded"
	, function()
	{
		var menu = document.getElementById("menu");
		if(menu)
		{
			var offset = menu.offsetTop;
			window.addEventListener(
				"scroll"
				, function()
				{
					if(window.pageYOffset > offset)
					{
						menu.style.position = "fixed";
						if(menu)
						{
							var li = menu.querySelector("#toneed");
							if(li)
							{
								li.innerHTML = '<a href="#top">Наверх</a>';
							}
						}
					}
					else
					{
						menu.style.position = "absolute";
						if(menu)
						{
							var li = menu.querySelector("#toneed");
							if(li)
							{
								li.innerHTML = '<a href="#bottom">Вниз</a>';
							}
						}
					}
				}
			);
		}
		function scrollToNeeded()
		{
			if(window.location.hash === "#top")
			{
				window.scrollTo(
					{
						top: 0,
						behavior: "smooth"
					}
				);
				if(menu)
				{
					var li = menu.querySelector("#toneed");
					if(li)
					{
						li.innerHTML = '<a href="#bottom">Вниз</a>';
					}
				}
			}
			else if(window.location.hash === "#bottom")
			{
				window.scrollTo(
					{
						top: document.body.scrollHeight
						, behavior: "smooth"
					}
				);
				if(menu)
				{
					var li = menu.querySelector("#toneed");
					if(li)
					{
						li.innerHTML = '<a href="#top">Наверх</a>';
					}
				}
			}
			else if(window.location.hash === "")
			{
				if(menu)
				{
					var li = menu.querySelector("#toneed");
					if(li)
					{
						li.innerHTML = '<a href="#bottom">Вниз</a>';
					}
				}	
			}
			else
			{
				if(menu)
				{
					var li = menu.querySelector("#toneed");
					if(li)
					{
						li.innerHTML = '<a href="#top">Наверх</a>';
					}
				}	
			}
		}
		scrollToNeeded();
		window.addEventListener("hashchange", scrollToNeeded);
	}
);