(function($) {
 
    $.fn.videoPlayer = function(options) { // videoPlayer is the name of our plugin

        var settings = {  
            playerWidth : '0.95', // Default is 95%
            videoClass : 'video'  // Video Class
        }
        // Extend the options so they work with the plugin
        if(options) {
            $.extend(settings, options);
        }
		
		var mediaPlayer,
			currentTrack = "0",
			videos, videoNodes;
         
        // For each so that we keep chainability.
        return this.each(function() {
			

		function create(itemList)
		{
			var div = document.createElement("div"),
				imageContainer = document.createElement("div"),
				videoIformation = document.createElement("idv"),
				img = document.createElement("img"),
				title = document.createElement('p'),
				duration = document.createElement("span");
			div.setAttribute("id", itemList.id);
			div.setAttribute("class", "video");
			imageContainer.setAttribute("class", "video-imagen");
			img.setAttribute("src", itemList.img);
			imageContainer.appendChild(img);
			div.appendChild(imageContainer);
			videoIformation.setAttribute("class", "video-information");
			title.textContent = itemList.name;
			duration.textContent = itemList.duration;
			videoIformation.appendChild(title);
			videoIformation.appendChild(duration);
			div.appendChild(videoIformation); 
			return div; 
		}
		
		function loadList(playlist)
		{
			var domPlayList = $('#playlist')[0],
				fragmentList = document.createDocumentFragment();
			for (let video of playlist)
				fragmentList.appendChild(create(video))
				domPlayList.appendChild(fragmentList)
				videoNodes = document.querySelectorAll('.video');
		}

		function higlight(current, lastTrack)
		{
			if (lastTrack)
			{
				videoNodes[lastTrack].classList.remove("playing");
				videoNodes[current].classList.add("playing");
			} 
			else
			{
				videoNodes[current].classList.add("playing");
			}
			currentTrack = current;
		}
		
		var getVideos = function()
			{
				getJSON
				(
					'source/videos.json',
					function(response)
					{
						videos = response;
						loadList(videos);
						higlight(currentTrack);
					}
				);
			};
		function getJSON(url, callback)
		{
			var request = new XMLHttpRequest();
			request.open('GET', url, true);
			request.onload = function()
			{
				if (request.status >= 200 && request.status < 400)
				{
					var data = JSON.parse(request.responseText);
					callback(data);
				}
			};
			request.onerror = function()
			{
				console.error("there was an error")
			};
			request.send();
		}
		
		getVideos();
			
        });
    }
     
})(jQuery);