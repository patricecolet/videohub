var player=(function(){var play=document.getElementById("play"),fullscreen=document.getElementById("expand"),playlist=document.getElementById("playlist"),audioVolume=document.getElementById("audioVolume"),progress=document.getElementById('progress'),start=document.getElementById('start'),finish=document.getElementById('finish'),preload=document.getElementById('preload');var mediaPlayer,currentTrack="0",videos,videoNodes;play.addEventListener("click",(e)=>{if(mediaPlayer.paused||mediaPlayer.ended){e.target.setAttribute("class","fas fa-pause");mediaPlayer.play();}else{mediaPlayer.pause();e.target.setAttribute("class","fas fa-play");}});audioVolume.addEventListener("click",(e)=>{if(mediaPlayer.muted){e.target.setAttribute('class','fas fa-volume-up')
mediaPlayer.muted=!mediaPlayer.muted;}else{e.target.setAttribute('class','fas fa-volume-off')
mediaPlayer.muted=!mediaPlayer.muted;}});fullscreen.addEventListener("click",()=>{if(mediaPlayer.requestFullscreen)mediaPlayer.requestFullscreen();else if(mediaPlayer.webkitRequestFullscreen)mediaPlayer.webkitRequestFullscreen();else if(mediaPlayer.mozRequestFullScreen)mediaPlayer.mozRequestFullScreen();else if(mediaPlayer.msRequestFullscreen)mediaPlayer.msRequestFullscreen();});playlist.addEventListener("click",function(e){var target=e.target;if(target.id!="playlist"){var id=target.closest(".video").id,el=target.closest(".video");preload.classList.add('lds-ring');higlight(id,currentTrack.toString());set(videos[id].source)
mediaPlayer.addEventListener('canplay',videoIsReady)}});function set(video){mediaPlayer.src=video;}
function handleProgress(e){var minutes=Math.floor(mediaPlayer.currentTime/60),seconds=Math.floor(mediaPlayer.currentTime-minutes*60),x=minutes<10?"0"+minutes:minutes,y=seconds<10?"0"+seconds:seconds;progress.style.width=Number(this.currentTime/this.duration*100)+"%";start.textContent=x+" : "+y;}
function togglePlay(event){event.preventDefault();if(mediaPlayer.paused||mediaPlayer.ended)mediaPlayer.play();else mediaPlayer.pause();}
function create(itemList){var div=document.createElement("div"),imageContainer=document.createElement("div"),videoIformation=document.createElement("idv"),img=document.createElement("img"),title=document.createElement('p'),duration=document.createElement("span");div.setAttribute("id",itemList.id)
div.setAttribute("class","video");imageContainer.setAttribute("class","video-imagen")
img.setAttribute("src",itemList.img);imageContainer.appendChild(img);div.appendChild(imageContainer);videoIformation.setAttribute("class","video-information");title.textContent=itemList.name;duration.textContent=itemList.duration;videoIformation.appendChild(title);videoIformation.appendChild(duration);div.appendChild(videoIformation);return div;}
function loadList(playlist){var domPlayList=document.getElementById('playlist'),fragmentList=document.createDocumentFragment();for(let video of playlist)
fragmentList.appendChild(create(video))
domPlayList.appendChild(fragmentList)
videoNodes=document.querySelectorAll('.video');}
function videoIsReady(event){preload.classList.remove('lds-ring');mediaPlayer.play();}
function higlight(current,lastTrack){if(lastTrack){videoNodes[lastTrack].classList.remove("playing");videoNodes[current].classList.add("playing");}else{videoNodes[current].classList.add("playing");}
currentTrack=current;}
var getVideos=function(){getJSON('assets/librairies/videoPlayer-master/source/videos.json',function(response){videos=response;loadList(videos)
higlight(currentTrack);});}
function getJSON(url,callback){var request=new XMLHttpRequest();request.open('GET',url,true);request.onload=function(){if(request.status>=200&&request.status<400){var data=JSON.parse(request.responseText);callback(data);}};request.onerror=function(){console.error("there was an error")};request.send();}
return{init:function(){mediaPlayer=document.getElementById("media-video");mediaPlayer.controls=false;mediaPlayer.muted=true;getVideos();mediaPlayer.addEventListener('canplay',videoIsReady)
mediaPlayer.addEventListener('timeupdate',handleProgress)
mediaPlayer.addEventListener('click',togglePlay)}}})();document.addEventListener("DOMContentLoaded",()=>{player.init();},false);