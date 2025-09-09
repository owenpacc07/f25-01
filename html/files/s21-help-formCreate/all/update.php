<html>
	<head>
		<style>
		a{
    			display: block;
    			width: 150px;
    			height: 15px;
    			background: #4E9CAF;
    			padding: 10px;
    			text-align: center;
    			border-radius: 5px;
    			color: white;
    			font-weight: bold;
    			line-height: 25px;
		}
        /* Style the close button */
#mine
{
    background-color:lightblue;
    width:400px;
}
/* .closediv {
  position: absolute;
  margin-top:0px;
  margin-left:25px;
  padding: 0px 16px 12px 0px;
  float:right;
} */
.closediv {
    background-color:blue;
  
}
.edit
{
    background-color:blue;
   
   
   
}

	</style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js" integrity="sha384-SR1sx49pcuLnqZUnnPwx6FCym0wLsk5JZuNx2bPPENzswTNFaQU1RDvt3wT4gWFG" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.min.js" integrity="sha384-j0CNLUeiqtyaRmlzUHCPZ+Gy5fQu0dQ6eZ/xAww941Ai1SxSY+0EQqNXNE6DZiVc" crossorigin="anonymous"></script>

    <script>
       
        function hello() {
            
            var mainelement = document.getElementById('formelements');
            var childrenn = mainelement.children;
            
            var str="";
            var ele="",span,txt;
            
            for(i=0;i<childrenn.length;i++)
            {
               
                tagname = childrenn[i].tagName.toLowerCase();
               
                if(tagname=="h1" || tagname=="h2" || tagname=="h3")
                {
                   try{
                        console.log(childrenn[i].nextSibling.nodeName)
                        if(childrenn[i].nextSibling.nodeName=="#text")
                        {
                            
                            if(childrenn[i].nextSibling.nextSibling)
                            {
                                if(childrenn[i].nextSibling.nextSibling.nodeName.toLowerCase()=="input")
                                {
                                    
                                        str = document.createElement('div');
                                        str.setAttribute('code','e02');
                                        id = Math.floor((Math.random() * 1000000) + 1);
                                        str.setAttribute('id',id)
                                        //str.className = 'close';
                                        document.getElementById('mine').appendChild(str);
                                        $('#'+id).append($(childrenn[i]).prop('outerHTML'))
                                        $('#'+id).append($(childrenn[i+1]).prop('outerHTML'))
                                        i+=1 
                                    
                                }
                                else
                                {
                                        str = document.createElement('div');
                                        str.setAttribute('code','e01');
                                        id = Math.floor((Math.random() * 1000000) + 1);
                                        str.setAttribute('id',id)
                                        //str.className = 'close';
                                        document.getElementById('mine').appendChild(str);
                                        $('#'+id).append($(childrenn[i]).prop('outerHTML'))
                                }
                                
                            }
                            else{
                                str = document.createElement('div');
                                str.setAttribute('code','e01');
                                id = Math.floor((Math.random() * 1000000) + 1);
                                str.setAttribute('id',id)
                                //str.className = 'close';
                                document.getElementById('mine').appendChild(str);
                                $('#'+id).append($(childrenn[i]).prop('outerHTML'))
                            }     
                        }
                        else
                        {
                            if(childrenn[i].nextSibling.nodeName.toLowerCase()=="input")
                            {
                                
                                str = document.createElement('div');
                                str.setAttribute('code','e02');
                                id = Math.floor((Math.random() * 1000000) + 1);
                                str.setAttribute('id',id)
                                //str.className = 'close';
                                document.getElementById('mine').appendChild(str);
                                $('#'+id).append($(childrenn[i]).prop('outerHTML'))
                                $('#'+id).append($(childrenn[i+1]).prop('outerHTML'))
                                i+=1 
                            }
                            else{
                                str = document.createElement('div');
                                str.setAttribute('code','e01');
                                id = Math.floor((Math.random() * 1000000) + 1);
                                str.setAttribute('id',id)
                                document.getElementById('mine').appendChild(str);
                                $('#'+id).append($(childrenn[i]).prop('outerHTML'))
                            }
                           
                        }
                    }
                    catch(err)
                    {
                        console.log(err);
                        //alert('add more elements to edit')
                    }
                   
                    
                }
                else if(tagname=="p")
                {
                    child = childrenn[i].children[0];
                    code = child.getAttribute('for').toLowerCase();
                    codes = {'e03':7,'e04':7,'e05':2,'e07':10,'e08':10,'e09':2,'e10':13,'e11':13,'e12':2};
                    val = codes[code];
                    str = document.createElement('div');
                    str.setAttribute('code',code);
                    id = Math.floor((Math.random() * 1000000) + 1);
                    str.setAttribute('id',id)
                    
                    document.getElementById('mine').appendChild(str);
                    
                    ele = "";
                    for(j=i;j<i+val;j++)
                    {
                        ele+=$(childrenn[j]).prop('outerHTML');
                       
                    }   
                    $('#'+id).append(ele);
                   i = i+(val-1);
                    
                    
                    

                }
                else if(tagname=="input") 
                {
                    type = $(childrenn[i]).attr('type')
                    if(type=="button" || type=="submit")
                    {
                        str = document.createElement('div');
                        str.setAttribute('code','e06');
                        //str.className = 'close';
                        id = Math.floor((Math.random() * 1000000) + 1);
                        str.setAttribute('id',id)
                        document.getElementById('mine').appendChild(str);
                        $('#'+id).append($(childrenn[i]).prop('outerHTML'))
                    }
                    //submit button
                   
                    
                }
                
                
                span = document.createElement("button");
                txt = document.createTextNode("Delete");
                span.className = "closediv";
                span.appendChild(txt);
                button = document.createElement('button');
                button.className = 'edit';
                t = document.createTextNode("Edit");
                button.appendChild(t);
                
                
                $(str).append(button);
                $(str).append(span);
                
            }
            
            $('#formelements').remove();
            closeitems();
            edititems();
        }
    </script>
	</head>
	<body>
        <div id="formelements">
            <?php
                $file = fopen("code.html","r");
                
                while(! feof($file))
                {
                    echo fgets($file);
                    
                    
                }
            
                fclose($file);
                
            ?>
        </div>
        <button onclick="save()">Save Page</button>

        <div id="mine"></div>
        <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-lg">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          
          <h4 class="modal-title" id="mtitle">Modal Header</h4>
        </div>
        <div class="modal-body" id="mbody">
          <p>Some text in the modal.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default"onclick="$('#myModal').modal('hide');" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" onclick="update()">Save changes</button>
        </div>
      </div>
      
    </div>
  </div>
   
        
   <script src="updateitems.js"></script>
   <script src="update.js"></script>

	<script>
        hello()
        function closeitems() {
            var close = document.getElementsByClassName("closediv");
            var i;
            for (i = 0; i < close.length; i++) {
                close[i].onclick = function() {
                    var div = this.parentElement;
                    div.remove()
                }
            }
            
        }
        function edititems() {
            var edit = document.getElementsByClassName("edit");
            var mapper = {
                'e01':1,'e02':2,'e03':3,'e04':4,'e05':5,'e06':6,'e07':7,'e08':8,'e09':9,'e10':10,'e11':11,'e12':12
            }
            for (i = 0; i < edit.length; i++) {
                edit[i].onclick = function() {
                    var div = this.parentElement;
                    code = $(div).attr('code');
                    val = mapper[code]
                    switch(val) {
                        case 1://update label
                                ulabel(div)
                                break;
                        case 2://update text input
                                console.log('hello');
                                utext(div)
                                break;
                        case 3://update radio buttons
                                uradio(div,2)
                                break;
                        case 4://update checkboxes
                                ucheckbox(div,2)
                                break;
                        case 5://update select fields
                                uselect(div,2)
                                break;
                        case 6://update buttons
                                usubmit(div)
                                break;
                        case 7:uradio(div,3);
                                break;
                        case 8:ucheckbox(div,3)
                                break;
                        case 9:uselect(div,3)
                                break;
                        case 10:uradio(div,4)
                                break;
                        case 11:ucheckbox(div,4)
                                break;
                        case 12:uselect(div,4)
                                break;
                        default:console.log("Not found");
                    }
                }
            }
        }
    </script>  
    </body>
    
</html>	