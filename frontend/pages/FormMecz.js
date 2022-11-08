import { ScrollView, View, Text, TouchableWithoutFeedback, StyleSheet, TextInput } from "react-native";
import Datastore from 'react-native-local-mongodb';
import AsyncStorage from '@react-native-async-storage/async-storage';
import DatePicker, { getFormatedDate } from 'react-native-modern-datepicker';
import BottomBar from "../components/BottomBar";
import ReturnArrowIco from "../svg/ReturnArrowIco";

const FormMecz = ({navigation})=>{
    const db = new Datastore({ filename: 'user', storage: AsyncStorage, autoload: true });
    const message = {
        login: "",
        password: "",
        name: "",
        sport: "",
        advancement: "",
        location: "",
        time: "",
        price: "",
        people_counter: ""
    }
    const nameChange = text=>{message.name = text}
    const sportChange = text=>{message.sport = text}
    const advancementChange = text=>{message.advancement = text}
    const locationChange = text=>{message.location = text}
    const timeChange = text=>{message.time = text.replaceAll('/','-')+":00"}
    const priceChange = text=>{message.price = text}
    const people_counterChange = text=>{message.people_counter = text}

    const add = ()=>{
        db.find({}, (err, docs)=>{
            message.login = docs[0].login;
            message.password = docs[0].password;
            console.log(message);
            fetch("http://10.0.2.2:8000/api/game/create", {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                method: "POST",
                body: JSON.stringify(message)
            })
            .then(response => response.json())
            .then(data =>{
                console.log(data);
                if(data.status="ok") {
                    navigation.navigate("Tablica");
                }else{
                    // Jak coś po stronie API nie wyszło
                }
            })
            .catch(err=>console.log);
        });
    }
    return (
        <ScrollView style={style.background}>
            <View style={style.main}>
                <TouchableWithoutFeedback onPress={()=>{navigation.navigate("Tablica")}}>
                    <View style={style.topBar}>
                        <ReturnArrowIco />
                    </View>
                </TouchableWithoutFeedback>
                <Text style={[style.text, style.label]}>Nazwa</Text>
                <View style={style.formContainer}>
                    <TextInput style={style.formText} onChangeText={nameChange} />
                </View>
                <Text style={[style.text, style.label]}>Sport</Text>
                <View style={style.formContainer}>
                    <TextInput style={style.formText} onChangeText={sportChange}  />
                </View>
                <Text style={[style.text, style.label]}>Zaawansowanie (0-10)</Text>
                <View style={style.formContainer}>
                    <TextInput style={style.formText} keyboardType='numeric' onChangeText={advancementChange}  />
                </View>
                <Text style={[style.text, style.label]}>Lokacja</Text>
                <View style={style.formContainer}>
                    <TextInput style={style.formText} onChangeText={locationChange}  />
                </View>
                <Text style={[style.text, style.label]}>Czas</Text>
                <View style={style.formContainer}>
                <DatePicker
                    onSelectedChange={timeChange}
                    options={{
                        backgroundColor: '#1c1c1c',
                        textHeaderColor: 'white',
                        textDefaultColor: 'white',
                        mainColor: '#F4722B'
                    }}
                    current={getFormatedDate(new Date(), 'YYYY-MM-DD h:m')}
                    selected={getFormatedDate(new Date(), 'YYYY-MM-DD h:m')}
                    minuteInterval={5}
                />
                </View>
                <Text style={[style.text, style.label]}>Płatne</Text>
                <View style={style.formContainer}>
                    <TextInput style={style.formText} keyboardType='numeric' onChangeText={priceChange}  />
                </View>
                <Text style={[style.text, style.label]}>Ilość miejsc</Text>
                <View style={style.formContainer}>
                    <TextInput style={style.formText} keyboardType='numeric' onChangeText={people_counterChange}  />
                </View>
                <View style={style.center}>
                    <TouchableWithoutFeedback onPress={add}>
                        <View style={style.button}>
                            <Text style={style.text}>Dodaj</Text>
                        </View>
                    </TouchableWithoutFeedback>
                </View>
                <BottomBar navigation={navigation} activeOption="" />
            </View>
        </ScrollView>
    )
}

const style = StyleSheet.create({
    background: {
        backgroundColor: "#1c1c1c"
    },
    main: {
        width: "100%",
        height: "100%",
        flexDirection: "column",
        backgroundColor: "#1c1c1c"
    },
    topBar: {
        width: "100%",
        paddingTop: 10,
        paddingLeft: 10,
        alignItems: "flex-start",
        marginBottom: 25
    },
    button: {
        justifyContent: "center",
        alignItems: "center",
        borderWidth: 2,
        borderColor: "white",
        width: 100,
        height: 50,
        borderRadius: 10,
        marginTop: 15,
        marginBottom: 25
    },
    text: {
        color: "white",
        fontSize: 25
    },
    label: {
        marginLeft: 30
    },
    center: {
        width: "100%",
        flexDirection: "row",
        justifyContent: "center"
    },
    formContainer: {
        width: "100%",
        flexDirection: "row",
        justifyContent: "center",
        marginTop: 10,
        marginBottom: 10
    },
    formText: {
        color: "white",
        borderWidth: 1,
        borderColor: "white",
        borderRadius: 10,
        padding: 5,
        width: "85%"
    }
});

export default FormMecz;