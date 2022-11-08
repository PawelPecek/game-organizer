import { View, Text, StyleSheet, TouchableWithoutFeedback, TextInput } from 'react-native';
import Datastore from 'react-native-local-mongodb';
import AsyncStorage from '@react-native-async-storage/async-storage';

const FormChangePassword = ({navigation}) => {
    const db = new Datastore({ filename: 'user', storage: AsyncStorage, autoload: true });
    let newPassword = "";
    const newPasswordChange = text=>{newPassword = text;};
    const accept = ()=>{
        db.find({}, (err, docs)=>{
            fetch("http://10.0.2.2:8000/api/user/change/password", {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                method: "POST",
                body: JSON.stringify({
                    login: docs[0].login,
                    password: docs[0].password,
                    newPassword: newPassword
                })
            })
            .then(response => response.json())
            .then(data =>{
                if(data.status="ok") {
                    console.log(data)
                    db.remove({}, { multi: true }, ()=>{
                        db.insert({
                            login: docs[0].login,
                            password: newPassword
                        }, ()=>{
                            navigation.goBack()
                        });
                    });
                }else{
                    console.log(data);
                }
            })
            .catch(err=>console.log);
        });
    }
    const cancel = ()=>{
        navigation.goBack();
    }
    return (
        <View style={style.main}>
            <View style={style.headerContainer}>
                <Text style={style.headerText}>Ustaw nowe hasło</Text>
            </View>
            <View style={style.inputContainer}>
                <TextInput style={style.inputForm} onChangeText={newPasswordChange} />
            </View>
            <View style={style.optionRow}>
                <TouchableWithoutFeedback onPress={accept}>
                    <View style={style.optionContainer}>
                        <Text style={style.optionText}>Zmień</Text>
                    </View>
                </TouchableWithoutFeedback>
                <TouchableWithoutFeedback onPress={cancel}>
                    <View style={style.optionContainer}>
                        <Text style={style.optionText}>Anuluj</Text>
                    </View>
                </TouchableWithoutFeedback>
            </View>
        </View>
    )
}

const style = StyleSheet.create({
    main: {
        width: "100%",
        height: "100%",
        flexDirection: "column",
        justifyContent: "center",
        alignItems: "center",
        backgroundColor: "#1c1c1c"
    },
    headerContainer: {
        width: "100%",
        marginBottom: 40
    },
    headerText: {
        color: "white",
        textAlign: "center",
        fontSize: 20
    },
    inputContainer: {
        width: "100%",
        alignItems: "center"
    },
    inputForm: {
        width: "75%",
        color: "white",
        borderWidth: 1,
        borderColor: "white",
        borderRadius: 20,
        paddingTop: 10,
        paddingBottom: 10,
        paddingLeft: 10,
        paddingBottom: 10
    },
    optionRow: {
        width: "100%",
        flexDirection: "row",
        justifyContent: "space-around",
        marginTop: 10,
        marginBottom: 10
    },
    optionContainer: {
        width: 150,
        justifyContent: "center",
        alignItems: "center",
        borderWidth: 1,
        borderColor: "white",
        borderRadius: 20,
        paddingTop: 10,
        paddingBottom: 10,
        marginTop: 10,
        marginBottom: 10
    },
    optionText: {
        color: "white"
    }
});

export default FormChangePassword;
