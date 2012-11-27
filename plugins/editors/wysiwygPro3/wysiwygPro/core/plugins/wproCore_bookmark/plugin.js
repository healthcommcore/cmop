
function wproPlugin_wproCore_bookmark(){}
wproPlugin_wproCore_bookmark.prototype.init=function(EDITOR){
EDITOR.addButtonStateHandler('bookmarkproperties',wproPlugin_wproCore_bookmark_bsh);
};
function wproPlugin_wproCore_bookmark_bsh(EDITOR,srcElement,cid,inTable,inA,range){
return inA?(inA.getAttribute('name')?"wproReady":(inA.getAttribute('id')?"wproReady":"wproDisabled")):"wproDisabled";
}