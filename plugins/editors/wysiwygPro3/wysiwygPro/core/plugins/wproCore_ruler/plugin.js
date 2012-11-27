
function wproPlugin_wproCore_ruler(){}
wproPlugin_wproCore_ruler.prototype.init=function(EDITOR){
EDITOR.addButtonStateHandler('rulerproperties',wproPlugin_wproCore_ruler_bsh);
};
function wproPlugin_wproCore_ruler_bsh(EDITOR,srcElement,cid,inTable,inA,range){
return range.nodes[0]?(range.nodes[0].tagName=='HR'?"wproReady":"wproDisabled"):"wproDisabled";
}