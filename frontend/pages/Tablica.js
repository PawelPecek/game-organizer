import { useState, useEffect } from 'react';
import { View, Text, StyleSheet, ScrollView, TouchableWithoutFeedback } from 'react-native';
import Datastore from 'react-native-local-mongodb';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useIsFocused } from '@react-navigation/native';
import SearchIco from '../svg/SearchIco';
import AddIco from '../svg/AddIco';
import BottomBar from '../components/BottomBar';
import ItemTablica from '../components/ItemTablica';

const Tablica = ({navigation}) => {
    const db = new Datastore({ filename: 'user', storage: AsyncStorage, autoload: true });
    const [meczState, setMeczState] = useState([]);
    const isFocused = useIsFocused();
    useEffect(()=>{
        db.find({}, (err, docs)=>{
            if(isFocused) {
                fetch("http://10.0.2.2:8000/api/game/list", {
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
                        console.log(data)
                        setMeczState(data.data);
                    }else{
                        console.log(data);
                    }
                })
                .catch(err=>console.log);
            }
        });
    }, [isFocused]);

    const searchAction = ()=>{
        navigation.navigate("Search");
    }
    const addAction = ()=>{
        navigation.navigate("FormMecz");
    }

    return (
        <View style={style.main}>
            <View style={style.topBar}>
                <TouchableWithoutFeedback onPress={searchAction}>
                    <View>
                        <SearchIco />
                    </View>
                </TouchableWithoutFeedback>
                <TouchableWithoutFeedback onPress={addAction}>
                    <View>
                        <AddIco />
                    </View>
                </TouchableWithoutFeedback>
            </View>
            <ScrollView style={style.scrollView}>
                {
                    meczState.map(el=>
                        <ItemTablica 
                        key={el.id}
                        id={el.id}
                        navigation={navigation}
                        name={el.name} 
                        category={el.sport} 
                        advancement={el.advancement} 
                        location={el.location} 
                        time={el.time} 
                        pay={el.price} 
                        openPosition={el.people_counter}
                        users={el.users}
                        />
                    )
                }
            </ScrollView>          
            <BottomBar navigation={navigation} activeOption="Tablica" />
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
        width: "100%"
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
    }
});

export default Tablica;