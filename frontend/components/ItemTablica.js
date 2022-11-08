import { View, Text, StyleSheet, TouchableWithoutFeedback } from "react-native";
import AddIco from "../svg/AddIco";

const ItemTablica = ({id, navigation, name, category, advancement, location, time, pay, openPosition, users})=>{
    const open = ()=>{
        navigation.navigate("ItemTablicaPage", { id });
    }
    const join = ()=>{
        db.find({}, (err, docs)=>{
            fetch("", {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                method: "POST",
                body: JSON.stringify(docs[0])
             })
            .then(response => response.json())
            .then(data =>{
                if(data.status="ok") {
//                        console.log(data)
//                    setMeczState(data.data);
                }else{
                    console.log(data);
                }
            })
            .catch(err=>console.log);
        });
        navigation.navigate("ItemTablicaPage", { id });
    }
    return (
        <View style={style.main}>
            <View style={style.leftCol}>
                <TouchableWithoutFeedback onPress={open}>
                    <View>
                        <Text style={style.fontBig}>{name}</Text>
                        <Text style={style.fontSmall}>{"Sport: " + category}</Text>
                        <Text style={style.fontSmall}>{"Zaawansowanie: " + advancement}</Text>
                        <Text style={style.fontSmall}>{"Miejsce: " + location}</Text>
                        <Text style={style.fontSmall}>{"Czas: " + time}</Text>
                        <Text style={style.fontSmall}>{"Wejściówka: " + pay}</Text>
                        <Text style={style.fontSmall}>{"Ilość miejsc: " + users.length.toString() + "/" + openPosition}</Text>
                    </View>
                </TouchableWithoutFeedback>
            </View>
            <View style={style.rightCol}>
                <TouchableWithoutFeedback onPress={join}>
                    <View>
                        <AddIco />
                    </View>
                </TouchableWithoutFeedback>
            </View>
        </View>
    )
}

const style = StyleSheet.create({
    main: {
        width: "100%",
        flexDirection: "row",
        borderWidth: 2,
        borderColor: "#ebebeb",
        borderRadius: 20,
        marginBottom: 20
    },
    leftCol: {
        width: "80%",
        flexDirection: "column",
        justifyContent: "flex-start",
        alignItems: "flex-start",
        paddingLeft: 10,
        paddingTop: 10,
        paddingBottom: 10
    },
    rightCol: {
        flex: 1,
        justifyContent: "flex-end",
        alignItems: "flex-end",
        paddingRight: 10,
        paddingBottom: 10
    },
    fontBig: {
        color: "white",
        fontSize: 35
    },
    fontSmall: {
        color: "white",
        fontSize: 20
    }
});

export default ItemTablica;