import { useState, useEffect } from 'react';
import { View, ScrollView, Text, TouchableWithoutFeedback, StyleSheet } from 'react-native';
import Datastore from 'react-native-local-mongodb';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useIsFocused } from '@react-navigation/native';
import BottomBar from '../components/BottomBar';
import AddIco from '../svg/AddIco';
import ReturnArrowIco from '../svg/ReturnArrowIco';

const ItemTablicaPage = ({ route, navigation }) => {
    const db = new Datastore({ filename: 'user', storage: AsyncStorage, autoload: true });
    const [data, setData] = useState({
        name: "",
        category: "",
        advancement: "",
        location: "",
        time: "",
        price: "",
        people_counter: "",
        users: [],
        can_join_flag: true
    });
    const isFocused = useIsFocused();
    useEffect(()=>{
        db.find({}, (err, docs)=>{
            if(isFocused) {
                const data = {
                    login: docs[0].login,
                    password: docs[0].password,
                    id: route.params.id
                }
                fetch("http://10.0.2.2:8000/api/game/get", {
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    method: "POST",
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data =>{
                    if(data.status="ok") {
                        console.log(data)
                        setData({
                            name: data.data.name,
                            category: data.data.sport,
                            advancement: data.data.advancement.toString(),
                            location: data.data.location,
                            time: data.data.time,
                            price: data.data.price,
                            people_counter: data.data.people_counter,
                            users: data.data.users,
                            can_join_flag: data.data.users.include(docs[0].login)
                        });
                    }else{
                        console.log(data);
                    }
                })
                .catch(err=>console.log);
            }
        });
    }, [isFocused]);
    const returnAction = ()=>{
        navigation.goBack();
    }
    const joinAction = ()=>{

    }
    return (
        <View style={style.main}>
            <View style={style.topBar}>
                <TouchableWithoutFeedback onPress={returnAction}>
                    <View>
                        <ReturnArrowIco />
                    </View>
                </TouchableWithoutFeedback>
                <TouchableWithoutFeedback onPress={joinAction}>
                    <View style={data.can_join_flag ? style.buttonContainerJoin : style.buttonContainerCancel}>
                        <Text style={data.can_join_flag ? style.buttonTextJoin : style.buttonTextCancel}>{data.can_join_flag ? "Dołącz" : "Zrezygnuj"}</Text>
                    </View>
                </TouchableWithoutFeedback>
            </View>
            <ScrollView style={style.scrollView}>
                <View style={style.rowContainer}><Text style={style.rowText}>{ "Nazwa: " + data.name }</Text></View>
                <View style={style.rowContainer}><Text style={style.rowText}>{ "Sport: " + data.category }</Text></View>
                <View style={style.rowContainer}><Text style={style.rowText}>{ "Zaawansowanie: " + data.advancement + "/10" }</Text></View>
                <View style={style.rowContainer}><Text style={style.rowText}>{ "Miejsce: " + data.location }</Text></View>
                <View style={style.rowContainer}><Text style={style.rowText}>{ "Czas: " + data.time }</Text></View>
                <View style={style.rowContainer}><Text style={style.rowText}>{ "Cena: " + data.price }</Text></View>
                <View style={style.rowContainer}><Text style={style.rowText}>{ "Ilość wolnych miejsc: " + data.users.length.toString() + "/" + data.people_counter }</Text></View>
                <View style={style.rowContainer}><Text style={style.rowText}>Użytkownicy:</Text></View>
                {
                    data.users.map((el, ind)=>(<View key={ind} style={style.rowContainer}><Text style={style.rowText}>- {el}</Text></View>))
                }
            </ScrollView>
            <BottomBar navigation={navigation} activeOption="" />
        </View>
    )
}

const style = StyleSheet.create({
    main: {
        width: "100%",
        height: "100%",
        flexDirection: "column",
        alignItems: "center",
        backgroundColor: "#1c1c1c"
    },
    scrollView: {
        width: "100%",
        paddingTop: 20
    },
    topBar: {
        width: "100%",
        height: 65,
        paddingTop: 15,
        paddingLeft: 15,
        paddingRight: 15,
        flexDirection: "row",
        justifyContent: "space-between",
        alignContent: "center"
    },
    rowContainer: {
        width: "100%",
        paddingLeft: 20
    },
    rowText: {
        color: "white",
        fontSize: 20
    },
    buttonContainerJoin: {
        borderWidth: 2,
        borderColor: "green",
        padding: 10,
        borderRadius: 20
    },
    buttonTextJoin: {
        color: "green",
        fontSize: 20
    },
    buttonContainerCancel: {
        borderWidth: 2,
        borderColor: "red",
        padding: 10,
        borderRadius: 20
    },
    buttonTextCancel: {
        color: "red",
        fontSize: 20
    }
});

export default ItemTablicaPage;
